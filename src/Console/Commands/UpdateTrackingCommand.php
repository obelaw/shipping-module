<?php

namespace Obelaw\Shipping\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Obelaw\Shipping\CourierDefine;
use Obelaw\Shipping\Models\ShippingDocument;

class UpdateTrackingCommand extends Command
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'shipping:update:tracking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update tracking information for shipping documents.';

    /**
     * Signature with rich filtering & execution controls.
     */
    protected $signature = 'shipping:update:tracking
        {--courier= : Filter by courier code}
        {--status=* : Filter by courier_status (repeatable); if omitted uses config list}
        {--pending : Only documents with NULL courier_status}
        {--id=* : Specific document id(s)}
        {--number=* : Specific document number(s)}
        {--limit= : Max documents to process}
        {--chunk=100 : Chunk size}
        {--dry : Dry run (don\'t call integrations)}
    ';

    public function handle(): void
    {
        $started = microtime(true);

        $courier  = $this->option('courier');
        $statuses = Arr::wrap($this->option('status')); // may be []
        $pending  = (bool)$this->option('pending');
        $ids      = array_filter(Arr::wrap($this->option('id')));
        $numbers  = array_filter(Arr::wrap($this->option('number')));
        $limit    = $this->option('limit') ? (int)$this->option('limit') : null;
        $chunk    = max(1, (int)$this->option('chunk'));
        $dry      = (bool)$this->option('dry');

        // Default statuses from config if user did not pass --status or --pending
        if (!$pending && empty($statuses)) {
            $configStatuses = config('obelaw.shipping.tracking.courier_status', []);
            if (!empty($configStatuses)) {
                $statuses = $configStatuses;
            }
        }

        /** @var Builder $query */
        $query = ShippingDocument::query()->with(['order.account'])->whereNull('cancel_at');

        if ($courier) {
            $query->whereHas('order.account', fn($q) => $q->where('courier', $courier));
        }

        if ($pending) {
            $query->whereNull('courier_status');
        } elseif (!empty($statuses)) {
            $query->whereIn('courier_status', $statuses);
        }

        if ($ids) {
            $query->whereIn('id', $ids);
        }

        if ($numbers) {
            $query->whereIn('document_number', $numbers);
        }

        if ($limit) {
            $query->limit($limit); // limit before counting clone uses this too, adjusted below if needed
        }

        $total = (clone $query)->count();
        if ($total === 0) {
            $this->warn('No documents match filters.');
            return;
        }

        $this->info("Tracking update started (dry=" . ($dry ? 'yes' : 'no') . ") ...");
        $this->line('Filters: ' . json_encode([
            'courier' => $courier,
            'statuses' => $statuses,
            'pending' => $pending,
            'ids' => $ids,
            'numbers' => $numbers,
            'limit' => $limit,
            'chunk' => $chunk,
        ]));

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $success = 0; $failed = 0; $skipped = 0;
        $instances = []; // cache per (courier, account_id)

        $process = function ($collection) use (&$instances, &$success, &$failed, &$skipped, $bar, $dry) {
            foreach ($collection as $doc) {
                try {
                    $account = $doc->order?->account;
                    if (!$account) {
                        $skipped++; $bar->advance();
                        Log::warning('Tracking skip: missing account', ['document_id' => $doc->id]);
                        continue;
                    }
                    $courierCode = $account->courier;
                    $class = CourierDefine::getIntegrationClass($courierCode);
                    if (!$class || !class_exists($class)) {
                        $skipped++; $bar->advance();
                        Log::warning('Tracking skip: missing integration class', ['document_id' => $doc->id, 'courier' => $courierCode, 'class' => $class]);
                        continue;
                    }
                    $key = $courierCode.'-'.$account->id;
                    if (!isset($instances[$key])) {
                        $instances[$key] = new $class($account, $doc->order);
                    }
                    if ($dry) {
                        $success++; // simulate success
                    } else {
                        $instances[$key]->doTracking($doc);
                        $success++;
                    }
                } catch (\Throwable $e) {
                    $failed++; Log::error('Tracking failed', [ 'document_id' => $doc->id, 'error' => $e->getMessage() ]);
                    if ($this->output->isVerbose()) {
                        $this->line("\n  Document {$doc->id} failed: {$e->getMessage()}");
                    }
                } finally {
                    $bar->advance();
                }
            }
        };

        if ($limit && $limit <= $chunk) {
            $process($query->get());
        } else {
            $processed = 0;
            $query->orderBy('id')->chunkById($chunk, function ($docs) use (&$processed, $limit, $process, &$success, &$failed, &$skipped) {
                if ($limit) {
                    $remaining = $limit - ($success + $failed + $skipped);
                    if ($remaining <= 0) {
                        return false; // stop
                    }
                    $docs = $docs->take($remaining);
                }
                $processed += $docs->count();
                $process($docs);
            });
        }

        $bar->finish();
        $this->newLine(2);

        $elapsed = round(microtime(true) - $started, 2);
        $this->info('Tracking update summary');
        $this->line("  Total matched: $total");
        $this->line("  Success:       $success");
        $this->line("  Failed:        $failed");
        $this->line("  Skipped:       $skipped");
        $this->line("  Time:          {$elapsed}s");

        if ($failed > 0) {
            $this->warn('Some documents failed. See logs for details.');
        }
    }
}

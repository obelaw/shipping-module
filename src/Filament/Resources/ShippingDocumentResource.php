<?php

namespace Obelaw\Shipping\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Obelaw\Shipping\CourierDefine;
use Obelaw\Shipping\Filament\Clusters\ShippingCluster;
use Obelaw\Shipping\Filament\Resources\ShippingDocumentResource\Pages\ListShippingDocument;
use Obelaw\Shipping\Models\ShippingDocument;
use Obelaw\Twist\Tenancy\Concerns\HasDBTenancy;
use Throwable;

class ShippingDocumentResource extends Resource
{
    use HasDBTenancy;

    protected static ?string $model = ShippingDocument::class;
    protected static ?string $cluster = ShippingCluster::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        // dd(CourierDefine::getAllIntegrationClasses());
        return $table
            ->columns([
                ImageColumn::make('courier')
                    ->label('Courier')
                    ->getStateUsing(fn($record) => CourierDefine::getIcon($record->order->account->courier)) // هذا يحدد مصدر الصورة
                    ->circular(),

                TextColumn::make('order.serial')
                    ->label('#')
                    ->searchable(),

                TextColumn::make('document_number')
                    ->label('Document Number')
                    ->searchable(),

                TextColumn::make('courier_status')
                    ->label('Status')
                    ->getStateUsing(fn($record) => $record->courier_status ?? 'Pending') // هذا يحدد مصدر الصورة
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('courier_status')
                    ->options(ShippingDocument::whereNotNull('courier_status')->pluck('courier_status', 'courier_status'))
                    ->searchable()
            ])
            ->recordActions([
                Action::make('updateTracking')
                    ->label('Update Tracking')
                    ->action(function ($record) {
                        try {
                            $classInstance = CourierDefine::getIntegrationClass($record->order->account->courier);
                            $classInstance = new $classInstance($record->order->account, $record->order);
                            $classInstance->doTracking($record);
                        } catch (Throwable $th) {
                            Notification::make()
                                ->title($th->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('PrintLabel')
                    ->label('Print Label')
                    ->action(function ($record) {
                        try {
                            $classInstance = CourierDefine::getIntegrationClass($record->order->account->courier);
                            $classInstance = new $classInstance($record->order->account, $record->order);
                            $classInstance->doPrintLabel($record);
                        } catch (Throwable $th) {
                            Notification::make()
                                ->title($th->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('updateTrackingAll')
                        ->label('Update Tracking')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                try {
                                    $classInstance = CourierDefine::getIntegrationClass($record->order->account->courier);
                                    $classInstance = new $classInstance($record->order->account, $record->order);
                                    $classInstance->doTracking($record);
                                } catch (Throwable $th) {
                                    Notification::make()
                                        ->title($th->getMessage())
                                        ->danger()
                                        ->send();
                                }
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShippingDocument::route('/'),
        ];
    }
}

<?php

namespace Obelaw\Shipping;

use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Obelaw\OMS\Models\Order;
use Obelaw\Shipping\Facades\Shipper;
use Obelaw\Shipping\Models\CourierAccount;
use Twist\Base\BaseAddon;
use Twist\Concerns\InteractsWithMigration;
use Twist\Contracts\HasHooks;
use Twist\Contracts\HasMigration;
use Twist\View\TwistView;

class ShippingAddon extends BaseAddon implements HasMigration, HasHooks
{
    use InteractsWithMigration;

    protected $pathMigrations = __DIR__ . '/../database/migrations';

    public function register(Panel $panel): void
    {
        $panel
            ->discoverResources(
                in: __DIR__ . DIRECTORY_SEPARATOR . 'Filament' . DIRECTORY_SEPARATOR . 'Resources',
                for: 'Obelaw\\Shipping\\Filament\\Resources'
            )
            ->discoverClusters(
                in: __DIR__ . DIRECTORY_SEPARATOR . 'Filament' . DIRECTORY_SEPARATOR . 'Clusters',
                for: 'Obelaw\\Shipping\\Filament\\Clusters'
            );
    }

    public function hooks(): void
    {
        TwistView::registerRenderHook(
            'obelaw.oms.view-order.header.actions',
            function () {
                return Action::make('shipOrder')
                    ->label('Ship Order')
                    ->icon('heroicon-o-truck')
                    ->color(Color::Blue)
                    ->disabled(fn(Order $record) => $record->isCancel())
                    ->fillForm(fn(Order $record): array => [
                        'cod_amount' => $record->grand_total,
                    ])
                    ->schema([
                        Select::make('account_id')
                            ->label('Shipping Account')
                            ->options(CourierAccount::pluck('name', 'id'))
                            ->required()
                            ->placeholder('Select a shipping account')
                            ->helperText('Choose the courier account for shipping this order'),

                        TextInput::make('cod_amount')
                            ->label('COD Amount')
                            ->numeric()
                            ->prefix('EGP')
                            ->helperText('Cash on delivery amount'),
                    ])
                    ->action(function (array $data, Order $record): void {
                        try {
                            Shipper::createDeliveryOrder($record, $data['account_id'], $data['cod_amount']);

                            Notification::make()
                                ->title('Order Shipped Successfully')
                                ->body("Order #{$record->serials->first()?->serial} has been shipped.")
                                ->success()
                                ->send();
                        } catch (Exception $e) {
                            Notification::make()
                                ->title('Shipping Failed')
                                ->body('Failed to create delivery order: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalDescription('Are you sure you want to ship this order?');
            },
        );
    }
}

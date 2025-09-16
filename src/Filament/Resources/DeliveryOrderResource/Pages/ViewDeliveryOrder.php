<?php

namespace Obelaw\Shipping\Filament\Resources\DeliveryOrderResource\Pages;

use Throwable;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Obelaw\Shipping\CourierDefine;
use Obelaw\Shipping\Facades\Shipper;
use Obelaw\Shipping\Filament\Resources\DeliveryOrderResource;
use Obelaw\Shipping\Models\CourierAccount;
use Obelaw\Shipping\Models\DeliveryOrder;

class ViewDeliveryOrder extends ViewRecord
{
    protected static string $resource = DeliveryOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('selectAccount')
                ->label('Select Account')
                ->hidden(fn(DeliveryOrder $record) => !is_null($record->account_id))
                ->schema([
                    Select::make(name: 'account_id')
                        ->label(label: 'Account')
                        ->required()
                        ->options(CourierAccount::pluck('name', 'id'))
                        ->searchable(),
                ])
                ->action(function (array $data, DeliveryOrder $record): void {
                    $record->account_id = $data['account_id'];
                    $record->save();
                }),

            Action::make('ship')
                ->label('Ship it')
                ->hidden(fn(DeliveryOrder $record) => is_null($record->account_id))
                // ->disabled(fn(DeliveryOrder $record) => Shipper::isHasDocument($record))
                ->action(function (DeliveryOrder $record): void {
                    try {
                        $classInstance = CourierDefine::getIntegrationClass($record->account->courier);
                        $classInstance = new $classInstance($record->account, $record);
                        $classInstance->doShip();
                    } catch (Throwable $th) {
                        Notification::make()
                            ->title($th->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $infolist
            ->schema([
                Tabs::make('Tabs')->tabs([
                    Tab::make('Order Information')
                        ->icon('heroicon-m-user')
                        ->schema([
                            TextEntry::make('id')
                                ->label('#'),

                            TextEntry::make('cod_amount')
                                ->label('COD')
                                ->money('EGP'),
                        ]),

                    Tab::make('Shipment Information')
                        ->icon('heroicon-m-user')
                        ->schema([
                            TextEntry::make('shippable.id')
                                ->label('Order ID'),


                        ]),

                    Tab::make('Shipment Items')
                        ->icon('heroicon-m-user')
                        ->schema([
                            RepeatableEntry::make('shippable.items')
                                ->label('Items')
                                ->schema([
                                    TextEntry::make('name'),
                                    TextEntry::make('sku'),
                                    TextEntry::make('quantity'),
                                    TextEntry::make('unit_price'),
                                    TextEntry::make('unit_price')
                                        ->money('EGP'),
                                    TextEntry::make('row_price')
                                        ->money('EGP'),
                                ])
                                ->columns(2)


                        ]),
                ]),
            ])->columns(1);
    }
}

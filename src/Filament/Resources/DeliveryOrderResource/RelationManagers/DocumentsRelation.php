<?php

namespace Obelaw\Shipping\Filament\Resources\DeliveryOrderResource\RelationManagers;

use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Obelaw\Shipping\CourierDefine;
use Obelaw\Shipping\Models\CourierAccount;

class DocumentsRelation extends RelationManager
{
    protected static ?string $title = 'Documents';
    protected static ?string $icon = 'heroicon-o-archive-box';
    protected static string $relationship = 'documents';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.account.courier.name')
                    ->label('Courier'),

                TextColumn::make('order.account.name')
                    ->label('Account'),

                TextColumn::make('document_number')
                    ->label('Document Number')
                    ->searchable(),

                TextColumn::make('courier_status')
                    ->label('Courier Status'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Action::make('updateTracking')
                    ->label('Update Tracking')
                    ->action(function ($record) {
                        try {
                            $classInstance = CourierDefine::getIntegrationClass($record->order->account->courier);
                            $classInstance = new $classInstance($record->order->account, $record->order);
                            $classInstance->doTracking($record);
                        } catch (\Throwable $th) {
                            Notification::make()
                                ->title($th->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('PrintLabel')
                    ->label('Print Label')
                    ->action(function ($record) {
                        // $classInstance = new $record->order->account->courier->class_instance($record->order->account, $record->order);
                        // return $classInstance->doPrintLabel($record);

                        try {
                            $classInstance = CourierDefine::getIntegrationClass($record->order->account->courier);
                            $classInstance = new $classInstance($record->order->account, $record->order);
                            $classInstance->doPrintLabel($record);
                        } catch (\Throwable $th) {
                            Notification::make()
                                ->title($th->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('cancel')
                    ->label('Cancel')
                    ->visible(fn($record) => !$record->cancel_at && in_array($record->courier_status, config('obelaw.shipping.cancel.courier_status', [])))
                    ->requiresConfirmation()
                    ->color('danger')
                    ->action(function ($record) {
                        if (!in_array($record->courier_status, config('obelaw.shipping.cancel.courier_status', []))) {
                            return Notification::make()
                                ->title('Cannot cancel this shipment')
                                ->danger()
                                ->send();
                        }

                        try {
                            $classInstance = CourierDefine::getIntegrationClass($record->order->account->courier);
                            $classInstance = new $classInstance($record->order->account, $record->order);
                            $classInstance->doCancel($record);
                        } catch (\Throwable $th) {
                            Notification::make()
                                ->title($th->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('delete')
                    ->label('Delete')
                    ->visible(fn($record) => $record->cancel_at && in_array($record->courier_status, config('obelaw.shipping.cancel.courier_status', [])))
                    ->requiresConfirmation()
                    ->color('danger')
                    ->action(function ($record) {
                        if ($record->cancel_at) {
                            $record->delete();
                        }
                    }),
            ])
            ->groupedBulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}

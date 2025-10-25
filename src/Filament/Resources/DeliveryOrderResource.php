<?php

namespace Obelaw\Shipping\Filament\Resources;

use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Obelaw\Shipping\Filament\Clusters\ShippingCluster;
use Obelaw\Shipping\Filament\Resources\DeliveryOrderResource\Pages\ListDeliveryOrder;
use Obelaw\Shipping\Filament\Resources\DeliveryOrderResource\Pages\ViewDeliveryOrder;
use Obelaw\Shipping\Filament\Resources\DeliveryOrderResource\RelationManagers\DocumentsRelation;
use Obelaw\Shipping\Models\DeliveryOrder;
use Twist\Tenancy\Concerns\HasDBTenancy;

class DeliveryOrderResource extends Resource
{
    use HasDBTenancy;

    protected static ?string $model = DeliveryOrder::class;

    protected static ?string $cluster = ShippingCluster::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-map';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('serials.serial')
                    ->label('#')
                    ->searchable(),

                TextColumn::make('account.name')->searchable(),

                TextColumn::make('cod_amount')
                    ->money('EGP'),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            DocumentsRelation::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDeliveryOrder::route('/'),
            'view' => ViewDeliveryOrder::route('/{record}/view'),
        ];
    }
}

<?php

namespace Obelaw\Shipping\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Obelaw\Shipping\Filament\Clusters\ShippingCluster;
use Obelaw\Shipping\Filament\Resources\DeliveryOrderResource\Pages\ListDeliveryOrder;
use Obelaw\Shipping\Filament\Resources\DeliveryOrderResource\Pages\ViewDeliveryOrder;
use Obelaw\Shipping\Filament\Resources\DeliveryOrderResource\RelationManagers\DocumentsRelation;
use Obelaw\Shipping\Models\DeliveryOrder;
use Obelaw\Permit\Attributes\Permissions;
use Obelaw\Permit\Traits\PremitCan;

#[Permissions(
    id: 'permit.shipping.deliveryorder.viewAny',
    title: 'Delivery Orders',
    description: 'Access on delivery order at shipping',
    permissions: []
)]
class DeliveryOrderResource extends Resource
{
    use PremitCan;

    protected static ?array $canAccess = [
        'can_viewAny' => 'permit.shipping.deliveryorder.viewAny',
    ];

    protected static ?string $model = DeliveryOrder::class;

    protected static ?string $cluster = ShippingCluster::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

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
            ->actions([
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

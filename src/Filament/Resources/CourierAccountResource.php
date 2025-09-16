<?php

namespace Obelaw\Shipping\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Obelaw\Shipping\CourierDefine;
use Obelaw\Shipping\Filament\Clusters\ShippingCluster;
use Obelaw\Shipping\Filament\Resources\CourierAccountResource\Pages;
use Obelaw\Shipping\Filament\Resources\CourierAccountResource\Pages\CreateCourierAccount;
use Obelaw\Shipping\Filament\Resources\CourierAccountResource\Pages\EditCourierAccount;
use Obelaw\Shipping\Filament\Resources\CourierAccountResource\Pages\ListCourierAccounts;
use Obelaw\Shipping\Models\Courier;
use Obelaw\Shipping\Models\CourierAccount;
use Obelaw\Twist\Tenancy\Concerns\HasDBTenancy;

class CourierAccountResource extends Resource
{
    use HasDBTenancy;

    protected static ?string $model = CourierAccount::class;
    protected static ?string $cluster = ShippingCluster::class;
    protected static ?int $navigationSort = 77777;
    protected static string | \UnitEnum | null $navigationGroup = 'Configuration';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('courier')
                    ->label('Courier')
                    ->required()
                    ->options(function () {
                        $classes = CourierDefine::getAllIntegrationClasses();

                        return array_combine(array_keys($classes), array_keys($classes));
                    })
                    ->searchable(),

                TextInput::make('name')
                    ->required(),

                KeyValue::make('credentials')
                    ->required(),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('courier.name')->searchable(),
                TextColumn::make('name')->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListCourierAccounts::route('/'),
            'create' => CreateCourierAccount::route('/create'),
            'edit' => EditCourierAccount::route('/{record}/edit'),
        ];
    }
}

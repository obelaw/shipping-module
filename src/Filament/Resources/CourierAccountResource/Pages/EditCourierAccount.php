<?php

namespace Obelaw\Shipping\Filament\Resources\CourierAccountResource\Pages;

use Filament\Actions\DeleteAction;
use Obelaw\Shipping\Filament\Resources\CourierAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCourierAccount extends EditRecord
{
    protected static string $resource = CourierAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

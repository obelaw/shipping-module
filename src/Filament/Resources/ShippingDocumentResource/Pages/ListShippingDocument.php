<?php

namespace Obelaw\Shipping\Filament\Resources\ShippingDocumentResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Obelaw\Shipping\Filament\Resources\ShippingDocumentResource;

class ListShippingDocument extends ListRecords
{
    protected static string $resource = ShippingDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

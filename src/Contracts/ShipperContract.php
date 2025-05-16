<?php

namespace Obelaw\Shipping\Contracts;

use Obelaw\Shipping\Models\ShippingDocument;

interface ShipperContract
{
    public function ship($deliveryOrder);
    public function printLabel(ShippingDocument $document);
    public function tracking(ShippingDocument $document);
    public function cancel(ShippingDocument $document);
}

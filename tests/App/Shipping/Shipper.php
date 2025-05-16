<?php

namespace Obelaw\Shipping\Tests\App\Shipping;

use Obelaw\Shipping\Contracts\ShipperContract;
use Obelaw\Shipping\Couriers;
use Obelaw\Shipping\Models\ShippingDocument;

class Shipper extends Couriers implements ShipperContract
{
    public function ship($deliveryOrder)
    {
        //
        return 'ship';
    }

    public function printLabel(ShippingDocument $document)
    {
        //
        return 'printLabel';
    }

    public function tracking(ShippingDocument $document)
    {
        //
        return 'tracking';
    }

    public function cancel(ShippingDocument $document)
    {
        //
        return 'cancel';
    }
}

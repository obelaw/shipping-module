<?php

namespace Obelaw\Shipping\Contracts;

use Obelaw\Shipping\Models\DeliveryOrderAwb;

interface ShipperContract
{
    public function ship($deliveryOrder);
    public function printLabel(DeliveryOrderAwb $AWB);
    public function tracking(DeliveryOrderAwb $AWB);
    public function cancel(DeliveryOrderAwb $AWB);
}

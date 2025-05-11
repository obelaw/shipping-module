<?php

namespace Obelaw\Shipping\Traits;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use Obelaw\Shipping\Models\DeliveryOrder;

trait HasDeliveryOrder
{
    public function deliveryOrder(): MorphOne
    {
        return $this->morphOne(DeliveryOrder::class, 'shippable');
    }
}

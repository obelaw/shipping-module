<?php

namespace Obelaw\Shipping\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Obelaw\Shipping\Models\DeliveryOrder;

trait HasDeliveryOrder
{
    /**
     * Get the delivery order associated with the model.
     *
     * This method returns a MorphOne relationship, allowing for a single delivery order
     * to be associated with the model. It is useful for models that can have only one
     * delivery order, such as an order that has a single shipment.
     *
     * @return MorphOne
     */
    public function deliveryOrder(): MorphOne
    {
        return $this->morphOne(DeliveryOrder::class, 'shippable');
    }

    /**
     * Get all delivery orders associated with the model.
     *
     * This method returns a MorphMany relationship, allowing for multiple delivery orders
     * to be associated with the model. It is useful for models that can have multiple
     * delivery orders, such as an order that may have multiple shipments.
     *
     * @return MorphMany
     */
    public function deliveryOrders(): MorphMany
    {
        return $this->morphMany(DeliveryOrder::class, 'shippable');
    }
}

<?php

namespace Obelaw\Shipping\Services;

use Obelaw\Shipping\Services\DeliveryOrderService;

/**
 * Service class for handling shipper-related operations,
 * primarily focusing on the creation of delivery orders.
 */
class ShipperService
{
    /**
     * Creates a new delivery order associated with a given order.
     * The provided `$order` model must define a `deliveryOrder()` relationship (typically MorphMany or MorphOne)
     * that links to the `Obelaw\Shipping\Models\DeliveryOrder` model.
     *
     * @param \Illuminate\Database\Eloquent\Model $order The Eloquent model instance (e.g., an e-commerce order) to which the delivery order will be associated.
     * @param int $accountId The ID of the `CourierAccount` to be used for this delivery.
     * @param float $codAmount The cash on delivery amount for the order.
     * @return DeliveryOrderService An instance of DeliveryOrderService for the newly created delivery order.
     */
    public function createDeliveryOrder($order, $accountId, $codAmount)
    {
        $deliveryOrder = $order->deliveryOrder()->firstOrCreate([
            'account_id' => $accountId,
            'cod_amount' => $codAmount,
        ]);

        return new DeliveryOrderService($deliveryOrder);
    }
}

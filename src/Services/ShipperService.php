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
     *
     * @param mixed $order The order model instance to which the delivery order will be associated.
     *                     This model should have a `deliveryOrder()` morphMany relationship.
     * @param int $accountId The ID of the courier account to be used for this delivery.
     * @param float $codAmount The cash on delivery amount for the order.
     * @return DeliveryOrderService An instance of DeliveryOrderService for the newly created delivery order.
     */
    public function createDeliveryOrder($order, $accountId, $codAmount)
    {
        $deliveryOrder = $order->deliveryOrder()->create([
            'account_id' => $accountId,
            'cod_amount' => $codAmount,
        ]);

        return new DeliveryOrderService($deliveryOrder);
    }
}

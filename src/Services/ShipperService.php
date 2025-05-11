<?php

namespace Obelaw\Shipping\Services;

class ShipperService
{
    public function createDeliveryOrder($order, $accountId, $codAmount)
    {
        return $order->deliveryOrder()->create([
            'account_id' => $accountId,
            'cod_amount' => $codAmount,
        ]);
    }
}

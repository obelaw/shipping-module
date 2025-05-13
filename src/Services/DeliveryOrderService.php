<?php

namespace Obelaw\Shipping\Services;

use Obelaw\Shipping\Contracts\ShipperContract;
use Obelaw\Shipping\CourierDefine;
use Obelaw\Shipping\Models\DeliveryOrder;
use Obelaw\Shipping\Models\DeliveryOrderAwb;

/**
 * Service class for managing delivery orders and interacting with shipping couriers.
 */
class DeliveryOrderService
{
    /**
     * @var ShipperContract The instance of the active shipper courier.
     */
    private ShipperContract $courierInstance;

    /**
     * Constructor for the DeliveryOrderService.
     *
     * Initializes the service by creating an instance of the appropriate shipper courier based on the delivery order's account.
     *
     * @param DeliveryOrder $deliveryOrder The DeliveryOrder model instance.
     */
    public function __construct(private DeliveryOrder $deliveryOrder)
    {
        $courierClass = CourierDefine::getIntegrationClass($deliveryOrder->account->courier);
        $this->courierInstance = new $courierClass($deliveryOrder->account, $deliveryOrder);
    }

    /**
     * Initiates the shipping process for the delivery order.
     *
     * Delegates the shipping action to the underlying courier instance.
     *
     * @return mixed The result of the shipping operation. The specific return type depends on the implemented courier.
     */
    public function ship()
    {
        return $this->courierInstance->doShip();
    }

    /**
     * Prints the shipping label for a specific Air Waybill (AWB).
     *
     * Delegates the print label action to the underlying courier instance.
     *
     * @param DeliveryOrderAwb $AWB The DeliveryOrderAwb model instance for which to print the label.
     * @return mixed The result of the print label operation. The specific return type depends on the implemented courier.
     */
    public function printLabel(DeliveryOrderAwb $AWB)
    {
        return $this->courierInstance->doPrintLabel($AWB);
    }

    /**
     * Tracks the shipment status for a specific Air Waybill (AWB).
     *
     * Delegates the tracking action to the underlying courier instance.
     *
     * @param DeliveryOrderAwb $AWB The DeliveryOrderAwb model instance to track.
     * @return mixed The tracking information. The specific return type depends on the implemented courier.
     */
    public function tracking(DeliveryOrderAwb $AWB)
    {
        return $this->courierInstance->doTracking($AWB);
    }

    /**
     * Cancels a shipment associated with a specific Air Waybill (AWB).
     *
     * Delegates the cancellation action to the underlying courier instance.
     *
     * @param DeliveryOrderAwb $AWB The DeliveryOrderAwb model instance to cancel.
     * @return mixed The result of the cancellation operation. The specific return type depends on the implemented courier.
     */
    public function cancel(DeliveryOrderAwb $AWB)
    {
        return $this->courierInstance->doCancel($AWB);
    }

    // TODO create refund method
    // /**
    //  * Initiates a refund for the delivery order.
    //  *
    //  * Delegates the refund action to the underlying courier instance.
    //  *
    //  * @return mixed The result of the refund operation. The specific return type depends on the implemented courier.
    //  */
    // public function refund()
    // {
    //      return $this->courierInstance->doRefund();
    // }
}

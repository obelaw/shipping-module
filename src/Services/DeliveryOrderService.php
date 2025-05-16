<?php

namespace Obelaw\Shipping\Services;

use Obelaw\Shipping\Contracts\ShipperContract;
use Obelaw\Shipping\CourierDefine;
use Obelaw\Shipping\Models\DeliveryOrder;
use Obelaw\Shipping\Models\ShippingDocument;

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
     * Prints the shipping label for a specific Air Waybill (Document).
     *
     * Delegates the print label action to the underlying courier instance.
     *
     * @param ShippingDocument $document The DeliveryOrderAwb model instance for which to print the label.
     * @return mixed The result of the print label operation. The specific return type depends on the implemented courier.
     */
    public function printLabel(ShippingDocument $document)
    {
        return $this->courierInstance->doPrintLabel($document);
    }

    /**
     * Tracks the shipment status for a specific Air Waybill (Document).
     *
     * Delegates the tracking action to the underlying courier instance.
     *
     * @param ShippingDocument $document The DeliveryOrderAwb model instance to track.
     * @return mixed The tracking information. The specific return type depends on the implemented courier.
     */
    public function tracking(ShippingDocument $document)
    {
        return $this->courierInstance->doTracking($document);
    }

    /**
     * Cancels a shipment associated with a specific Air Waybill (Document).
     *
     * Delegates the cancellation action to the underlying courier instance.
     *
     * @param ShippingDocument $document The DeliveryOrderAwb model instance to cancel.
     * @return mixed The result of the cancellation operation. The specific return type depends on the implemented courier.
     */
    public function cancel(ShippingDocument $document)
    {
        return $this->courierInstance->doCancel($document);
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

    public function createDocument(string $documentNumber): ShippingDocument
    {
        return $this->deliveryOrder->document()->create([
            'document_number' => $documentNumber,
        ]);
    }

    /**
     * Checks if the given delivery order has an active (not canceled) Air Waybill (Document).
     *
     * @param DeliveryOrder $deliveryOrder The delivery order to check.
     * @return ShippingDocument|null The active DeliveryOrderAwb model if found, otherwise null.
     */
    public function isHasDocument(DeliveryOrder $deliveryOrder)
    {
        // @phpstan-ignore-next-line model relation Document() might not be strictly typed yet for static analysis
        return $deliveryOrder->document()->whereNull('cancel_at')->first();
    }
}

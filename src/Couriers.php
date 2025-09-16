<?php

namespace Obelaw\Shipping;

use Throwable;
use Exception;
use Obelaw\Shipping\Models\CourierAccount;
use Obelaw\Shipping\Models\DeliveryOrder;
use Obelaw\Shipping\Facades\Shipper;
use Obelaw\Shipping\Models\ShippingDocument;

/**
 * Abstract base class for courier integrations.
 *
 * This class provides a common structure and helper methods for specific courier implementations.
 * Concrete courier classes extending this class are expected to implement the following methods,
 * which are called by the `do*` methods in this base class:
 * - `ship(DeliveryOrder $deliveryOrder)`: Handles the actual shipping logic for the given delivery order.
 * - `printLabel(ShippingDocument $document)`: Handles label printing logic for the given shipping document.
 * - `tracking(ShippingDocument $document)`: Handles shipment tracking logic for the given shipping document.
 * - `cancel(ShippingDocument $document)`: Handles shipment cancellation logic for the given shipping document.
 */
abstract class Couriers
{
    /**
     * @param CourierAccount $account The courier account model.
     * @param DeliveryOrder $deliveryOrder The delivery order model.
     */
    public function __construct(
        public $account,
        public $deliveryOrder
    ) {}

    /**
     * Initiates the shipping process for the delivery order.
     * This method calls the abstract `ship` method which must be implemented by concrete courier classes.
     *
     * @return mixed The result of the shipping operation, typically specific to the courier implementation.
     */
    public function doShip()
    {
        try {
            return $this->ship($this->deliveryOrder);
        } catch (Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * Prints the shipping label for a specific Air Waybill (Document).
     * This method calls the abstract `printLabel` method which must be implemented by concrete courier classes.
     *
     * @param ShippingDocument $document The ShippingDocument model instance for which to print the label.
     * @return mixed The result of the print label operation, typically specific to the courier implementation.
     */
    public function doPrintLabel(ShippingDocument $document)
    {
        return $this->printLabel($document);
    }

    /**
     * Tracks the shipment status for a specific Air Waybill (Document).
     * This method calls the abstract `tracking` method which must be implemented by concrete courier classes.
     *
     * @param ShippingDocument $document The ShippingDocument model instance to track.
     * @return mixed The tracking information, typically specific to the courier implementation.
     */
    public function doTracking(ShippingDocument $document)
    {
        return $this->tracking($document);
    }

    /**
     * Cancels a shipment associated with a specific Air Waybill (Document).
     * This method calls the abstract `cancel` method (which must be implemented by concrete courier classes)
     * and then updates the Document record to mark it as canceled.
     *
     * @param ShippingDocument $document The ShippingDocument model instance to cancel.
     * @return void
     */
    public function doCancel(ShippingDocument $document)
    {
        $cancel = $this->cancel($document);

        if ($cancel) {
            $document->cancel_at = now();
            $document->save();
        }

        return $cancel;
    }

    /**
     * Retrieves account credentials.
     * Assumes `$this->account->credentials` is an array or an object that can be cast to an array.
     *
     * @param string|null $key The specific credential key to retrieve. If null, all credentials are returned as an array.
     * @return mixed|array If `$key` is provided, returns the corresponding credential value.
     *                     If `$key` is null, returns an array of all credentials.
     *                     If `$key` is provided but not found, behavior depends on PHP's array access (may raise a notice/error).
     */
    protected function accountCredentials($key = null)
    {
        $collection = collect($this->account->credentials)->toArray();

        if (!is_null($key)) {
            return $collection[$key];
        }

        return $collection;
    }

    /**
     * Creates and associates a new ShippingDocument (e.g., AWB) with the delivery order,
     * if an active one doesn't already exist.
     * It uses `Shipper::isHasDocument()` to check for an existing active Document on the given delivery order.
     *
     * @param string $documentNumber The document number (e.g., AWB number) to assign to the new shipping document.
     * @return void
     */
    protected function setDocumentNumber(string $documentNumber)
    {
        return $this->deliveryOrder->document()->create(['document_number' => $documentNumber]);
    }
}

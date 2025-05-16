<?php

use Obelaw\Shipping\Facades\Shipper;
use Obelaw\Shipping\Models\CourierAccount;
use Obelaw\Shipping\Tests\App\Models\Order;

test('shipping a delivery order returns the expected result from the courier', function () {
    $order = Order::create([
        'name' => 'test',
        'total' => 100
    ]);

    $courierAccount = CourierAccount::create([
        'courier' => 'Shipper',
        'name' => 'test',
        'credentials' => ['password' => '123456'],
    ]);


    $deliveryOrder = Shipper::createDeliveryOrder($order, $courierAccount->id, 100);

    expect($deliveryOrder->ship())->toBe('ship');
});


test('printing a shipping label returns the expected result from the courier', function () {
    $order = Order::create([
        'name' => 'test',
        'total' => 100
    ]);

    $courierAccount = CourierAccount::create([
        'courier' => 'Shipper',
        'name' => 'test',
        'credentials' => ['password' => '123456'],
    ]);


    $deliveryOrder = Shipper::createDeliveryOrder($order, $courierAccount->id, 100);

    $doc = $deliveryOrder->createDocument(555555);

    expect($deliveryOrder->printLabel($doc))->toBe('printLabel');
});

test('tracking a shipment returns the expected result from the courier', function () {
    $order = Order::create([
        'name' => 'test',
        'total' => 100
    ]);

    $courierAccount = CourierAccount::create([
        'courier' => 'Shipper',
        'name' => 'test',
        'credentials' => ['password' => '123456'],
    ]);


    $deliveryOrder = Shipper::createDeliveryOrder($order, $courierAccount->id, 100);

    $doc = $deliveryOrder->createDocument(555555);

    expect($deliveryOrder->tracking($doc))->toBe('tracking');
});

test('cancelling a shipment returns the expected result from the courier', function () {
    $order = Order::create([
        'name' => 'test',
        'total' => 100
    ]);

    $courierAccount = CourierAccount::create(attributes: [
        'courier' => 'Shipper',
        'name' => 'test',
        'credentials' => ['password' => '123456'],
    ]);


    $deliveryOrder = Shipper::createDeliveryOrder($order, $courierAccount->id, 100);

    $doc = $deliveryOrder->createDocument(555555);

    expect($deliveryOrder->cancel($doc))->toBe('cancel');
});

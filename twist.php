<?php

use Obelaw\Twist\Addons\AddonRegistrar;
use Obelaw\Shipping\ShippingAddon;

AddonRegistrar::register(
    'obelaw.shipping',
    ShippingAddon::class,
    config('obelaw.shipping.panels')
);

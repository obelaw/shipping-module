<?php

\Obelaw\Twist\Addons\AddonRegistrar::register(
    'obelaw.shipping',
    \Obelaw\Shipping\ShippingAddon::class,
    config('obelaw.shipping.panels')
);

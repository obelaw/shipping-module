<?php

namespace Obelaw\Shipping\Facades;

use Illuminate\Support\Facades\Facade;

class Shipper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'obelaw.shipping.services.shipper';
    }
}

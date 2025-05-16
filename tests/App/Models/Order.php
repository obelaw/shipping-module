<?php

namespace Obelaw\Shipping\Tests\App\Models;

use Illuminate\Database\Eloquent\Model;
use Obelaw\Shipping\Traits\HasDeliveryOrder;

class Order extends Model 
{
    use HasDeliveryOrder;

    protected $fillable = [
        'name',
        'total',
    ];
}
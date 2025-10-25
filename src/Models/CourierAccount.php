<?php

namespace Obelaw\Shipping\Models;

use Twist\Base\BaseModel;

class CourierAccount extends BaseModel
{
    protected $table = 'shipping_courier_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'courier',
        'name',
        'credentials',
    ];

    protected $casts = [
        'credentials' => 'array',
    ];
}

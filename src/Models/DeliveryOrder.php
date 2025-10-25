<?php

namespace Obelaw\Shipping\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Obelaw\Shipping\Models\ShippingDocument;
use Twist\Base\BaseModel;

class DeliveryOrder extends BaseModel
{
    protected $table = 'shipping_delivery_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_id',
        'cod_amount',
    ];

    public function account()
    {
        return $this->hasOne(CourierAccount::class, 'id', 'account_id');
    }

    public function shippable(): MorphTo
    {
        return $this->morphTo();
    }

    public function document()
    {
        return $this->hasOne(ShippingDocument::class, 'order_id', 'id');
    }

    public function documents()
    {
        return $this->hasMany(ShippingDocument::class, 'order_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(DeliveryOrderItem::class, 'order_id', 'id');
    }
}

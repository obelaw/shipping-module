<?php

namespace Obelaw\Shipping\Models;

use Obelaw\Twist\Base\BaseModel;

class ShippingDocument extends BaseModel
{
    protected $table = 'shipping_documents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'document_number',
        'document_file',
        'courier_status',
        'cancel_at',
    ];

    public function order()
    {
        return $this->hasOne(DeliveryOrder::class, 'id', 'order_id');
    }
}

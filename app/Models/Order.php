<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'receipt_number',
        'date',
        'customer_type_id',
        'total_amount',
        'tip',
        'discount',
        'ground_total',
        'status'
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function voidReason()
    {
        return $this->hasOne(VoidReason::class);
    }
}

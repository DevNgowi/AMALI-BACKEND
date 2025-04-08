<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['order_number', 'customer_type_id', 'customer_id', 'total_amount', 'status', 'date'];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function extraCharges()
    {
        return $this->hasMany(CartExtraCharge::class);
    }

    public function cartItem()
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'name', 'item_id', 'quantity','unit', 'amount'];

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}

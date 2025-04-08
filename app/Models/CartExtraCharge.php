<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartExtraCharge extends Model
{
    protected $fillable = ['cart_id', 'name', 'quantity', 'amount'];
}

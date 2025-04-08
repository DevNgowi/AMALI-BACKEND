<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderExtraCharge extends Model
{
    protected $fillable = [
        'order_id',
        'extra_charge_id',
    ];
}

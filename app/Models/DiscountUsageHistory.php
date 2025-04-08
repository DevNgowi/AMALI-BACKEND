<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountUsageHistory extends Model
{
    protected $fillable = [
        'discount_id', 'order_id', 'user_id', 'discount_amount', 'used_at'
    ];
}

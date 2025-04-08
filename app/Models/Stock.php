<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'item_id',
        'store_id',
        'max_quantity',
        'min_quantity'
    ];
}

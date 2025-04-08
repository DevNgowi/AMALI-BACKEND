<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPrice extends Model
{
    protected $table = 'item_prices';

    protected $fillable = [
        'item_id',
        'store_id',
        'unit_id',
        'amount'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemCost extends Model
{
    protected $fillable = [
        'item_id','store_id', 'unit_id', 'amount'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemTax extends Model
{
    protected $fillable = [
        'item_id', 'tax_id', 'store_id'
    ];

    
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemStore extends Model
{
    protected $fillable = [
        'store_id', 'item_id'
    ];
}

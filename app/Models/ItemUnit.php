<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemUnit extends Model
{
    protected $table = 'item_units';

    protected $fillable = [
        'item_id',
        'buying_unit_id', 
        'selling_unit_id',
    ];

    public function item()

    {
        return $this->belongsTo(Item::class);
    }


    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}

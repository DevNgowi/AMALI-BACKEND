<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandApplicableItem extends Model
{
    protected $fillable = [
        'brand_id', 'item_id'
    ];

    public function itemBrand(){
        return $this->belongsTo(ItemBrand::class);
    }

    public function items(){
        return $this->belongsTo(Item::class);
    }
}

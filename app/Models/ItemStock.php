<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemStock extends Model
{
    protected $fillable =[
        'item_id', 'stock_id', 'stock_quantity'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id', 'id');
   
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barcode extends Model
{
    protected $fillable = [
        'code'
    ];

    public function items()
{
    return $this->belongsToMany(Item::class, 'item_barcodes', 'barcode_id', 'item_id')
        ->withTimestamps();
}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemBrand extends Model
{
    /** @use HasFactory<\Database\Factories\ItemBrandFactory> */
    use HasFactory;

    protected $table = 'item_brands';

    protected $fillable = [
        'name', 'description', 'is_active'
    ];
}

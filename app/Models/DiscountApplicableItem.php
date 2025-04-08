<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountApplicableItem extends Model
{
    protected $fillable = ['discount_id', 'item_id'];
}

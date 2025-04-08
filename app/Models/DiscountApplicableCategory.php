<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountApplicableCategory extends Model
{
    protected $fillable = ['discount_id', 'category_id'];
}

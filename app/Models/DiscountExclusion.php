<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountExclusion extends Model
{
    protected $fillable = ['discount_id', 'excludable_type', 'excludable_id'];
}

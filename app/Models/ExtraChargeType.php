<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraChargeType extends Model
{
    protected $table = 'extra_charge_types';

    protected $fillable = [
        'name'
    ];
}

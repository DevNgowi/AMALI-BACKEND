<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraCharge extends Model
{
    /** @use HasFactory<\Database\Factories\ExtraChargeFactory> */
    use HasFactory;

    protected $table = 'extra_charges';

    protected $fillable = [
        'name',
        'amount',
        'tax_id',
        'charge_type_id',
    ];

    public function tax(){
        return $this->belongsTo(Tax::class, 'tax_id');

    }

    public function extraChargeType(){
        return $this->belongsTo(ExtraChargeType::class, 'charge_type_id');
        
    }
}

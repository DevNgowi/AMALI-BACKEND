<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    /** @use HasFactory<\Database\Factories\DiscountFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'discount_type_id',
        'value',
        'minimum_purchase_amount',
        'maximum_discount_amount',
        'is_active',
        'is_combinable',
        'starts_at',
        'expires_at',
        'usage_limit',
        'description',
        
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_combinable' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime'
    ];
    public function discountType()
    {
        return $this->belongsTo(DiscountType::class);
    }
}

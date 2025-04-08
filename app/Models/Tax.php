<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    /** @use HasFactory<\Database\Factories\TaxFactory> */
    use HasFactory;

    protected $table = 'taxes';

    protected $fillable = [
        'name',
        'tax_type',
        'tax_mode',
        'tax_percentage',
        'tax_amount'
    ];

    public function items() {
        return $this->belongsToMany(Item::class, 'item_taxes');
    }
}

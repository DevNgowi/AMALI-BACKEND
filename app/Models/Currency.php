<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'name',
        'sign',
        'sign_placement',
        'currency_name_in_words',
        'digits_after_decimal'
    ];

    /**
     * Get the display name of the currency.
     */

    /**
     * @return string
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->name} ({$this->code})";
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}

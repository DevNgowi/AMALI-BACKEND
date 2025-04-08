<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Vendor extends Model
{
    use HasFactory;
    //
    protected $table = 'vendors';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city_id',
        'state',
        'country_id',
        'postal_code',
        'contact_person',
        'tin',
        'vrn',
        'status',
    ];

    
    public function city(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

}

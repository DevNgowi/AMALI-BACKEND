<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_name',
        'country_id',
        'website',
        'email',
        'state',
        'phone',
        'post_code',
        'company_logo',
        'vrn_no',
        'tin_no',
        'address'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function fiscalYears()
    {
        return $this->hasMany(CompanyFiscalYear::class);
    }
}

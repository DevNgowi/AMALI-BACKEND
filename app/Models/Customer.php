<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_code',
        'customer_name',
        'customer_type_id',
        'city_id',
        'account_group',
        'opening_balance',
        'dr_cr',
        'mobile_sms_no',
        'email',
        'address',
        'credit_limit',
        'credit_period',
        'active',
        'account_ledger_creation'
    ];

    public function customerType()
    {
        return $this->belongsTo(CustomerType::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function cityLedger()
    {
        return $this->hasOne(CityLedger::class);
    }
}

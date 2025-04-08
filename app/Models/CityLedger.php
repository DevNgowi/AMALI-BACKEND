<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityLedger extends Model
{
    protected $fillable = [
        'customer_id',
        'ledger_code',
        'opening_balance',
        'balance_type',
        'current_balance',
        'active'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}

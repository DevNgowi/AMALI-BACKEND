<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'short_code', 
        'payment_method', 
        'payment_type_id'
    ];

    public function payment_type(){
        return $this->belongsTo(PaymentType::class);
    }
}

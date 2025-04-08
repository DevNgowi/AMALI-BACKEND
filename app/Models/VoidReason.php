<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoidReason extends Model
{
    protected $fillable = ['order_id', 'reason_id', 'details'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function reason()
    {
        return $this->belongsTo(Reason::class);
    }
}

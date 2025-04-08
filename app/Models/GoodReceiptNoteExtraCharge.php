<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodReceiptNoteExtraCharge extends Model
{
    protected $fillable = [
        'grn_id',
        'extra_charge_id',
        'unit_id',
        'amount',
        // ... any other fillable attributes ...
    ];

    /**
     * Get the good receive note that this extra charge belongs to.
     */
    public function goodReceiveNote()
    {
        return $this->belongsTo(GoodReceiptNote::class, 'grn_id');
    }

    /**
     * Get the extra charge type associated with this GRN extra charge.
     */
    public function extraCharge()
    {
        return $this->belongsTo(ExtraCharge::class, 'extra_charge_id'); 
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id'); 
    }

}

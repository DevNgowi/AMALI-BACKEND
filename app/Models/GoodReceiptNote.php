<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodReceiptNote extends Model
{
    protected $table = 'good_receipt_notes';

    protected $fillable = [ // If you are using mass assignment
        'grn_number',
        'purchase_order_id',
        'supplier_id',
        'received_by',
        'received_date',
        'status',
        'remarks',
        'delivery_note_number',
        // ... other fillable attributes ...
    ];

    /**
     * Get the purchase order associated with the GRN.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    /**
     * Get the supplier (vendor) associated with the GRN.
     */
    public function supplier()
    {
        return $this->belongsTo(Vendor::class, 'supplier_id');
    }

    public function goodReceiveNoteItems()
    {
        return $this->hasMany(GoodReceiveNoteItem::class, 'grn_id', 'id');
    }

    /**
     * Get the extra charges associated with the good receive note.
     */
    public function goodReceiveNoteExtraCharges()
    {
        return $this->hasMany(GoodReceiptNoteExtraCharge::class, 'grn_id', 'id');
    }
}

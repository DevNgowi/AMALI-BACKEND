<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodReceiveNoteItem extends Model
{
    protected $fillable = [
        // ... your other fillable attributes for GRN items ...
        'grn_id',
        'purchase_order_item_id',
        'item_id',
        'unit_id',
        'ordered_quantity',
        'received_quantity',
        'accepted_quantity',
        'rejected_quantity',
        'unit_price',
        'received_condition',
        // ... any other fillable attributes ...
    ];

    /**
     * Get the good receive note that this item belongs to.
     */
    public function goodReceiveNote()
    {
        return $this->belongsTo(GoodReceiptNote::class, 'grn_id');
    }

    /**
     * Get the purchase order item associated with this GRN item (can be nullable).
     */
    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'purchase_order_item_id');
    }

    /**
     * Get the item associated with this GRN item.
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * Get the unit of measure for this GRN item.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}

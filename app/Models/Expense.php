<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_category_id',
        'payment_type_id',
        'vendor_id',
        'expense_date',
        'amount',
        'currency',
        'description',
        'reference_number',
        'receipt_path',
        'purchase_order_id',
        'inventory_item_id',
        'pos_location_id',
        'is_reimbursable',
        'notes',
        // ... other fillable attributes ...
    ];

    /**
     * Get the expense category associated with the expense.
     */
    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    /**
     * Get the payment method associated with the expense.
     */
    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }

    /**
     * Get the vendor associated with the expense.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

}

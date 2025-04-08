<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_category_id')->constrained('expense_categories');
            $table->foreignId('payment_type_id')->constrained('payment_types'); 
            $table->foreignId('vendor_id')->nullable()->constrained('vendors');
            $table->foreignId('user_id')->constrained('users'); 
            $table->foreignId('expense_status_id')->constrained('expense_statuses'); 
            $table->date('expense_date');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('TZS');
            $table->text('description')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('receipt_path')->nullable();
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders');
            $table->foreignId('item_id')->nullable()->constrained('items');
            $table->foreignId('pos_location_id')->nullable()->constrained('pos_locations');
            $table->string('approval_status')->default('Pending');
            $table->timestamp('approval_date')->nullable();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users');
            $table->date('payment_date')->nullable();
            $table->foreignId('payment_account_id')->nullable()->constrained('payment_accounts');
            $table->decimal('tax_rate', 5, 2)->nullable();
            $table->decimal('tax_amount', 15, 2)->nullable();
            $table->string('tax_code')->nullable();
            $table->boolean('is_reimbursable')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};

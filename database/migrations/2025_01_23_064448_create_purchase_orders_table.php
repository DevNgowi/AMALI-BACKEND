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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); 
            $table->foreignId('supplier_id')->constrained('vendors')->onUpdate('cascade')->onDelete('cascade');
            $table->date('order_date'); 
            $table->date('expected_delivery_date')->nullable(); 
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Completed', 'Cancelled', 'Partially_received'])->default('Pending');
            $table->decimal('total_amount', 10, 2)->default(0.00); 
            $table->string('currency', 10)->default('USD'); 
            $table->text('notes')->nullable(); // Additional Notes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};

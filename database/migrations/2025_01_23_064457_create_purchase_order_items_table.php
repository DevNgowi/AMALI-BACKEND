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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade'); // Foreign Key to Purchase Orders Table
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade'); // Foreign Key to Items Table
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade'); // Foreign Key to Units Table
            $table->integer('quantity')->unsigned();
            $table->integer(column: 'discount')->nullable();
            $table->decimal('unit_price', 10, 2); 
            // $table->foreignId('discount_id')->nullable()->constrained('discounts')->onDelete('set null'); // Foreign Key to Discounts Table
            $table->foreignId('tax_id')->nullable()->constrained('taxes')->onDelete('set null'); // Foreign Key to Taxes Table
            $table->decimal('total_price', 10, 2); 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};

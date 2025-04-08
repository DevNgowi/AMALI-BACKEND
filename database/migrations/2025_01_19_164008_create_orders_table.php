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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50);
            $table->string('receipt_number', 50);
            $table->dateTime('date');
            $table->unsignedBigInteger('customer_type_id');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('tip', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->foreign('customer_type_id')->references('id')->on('customer_types')->onUpdate('cascade');
            $table->decimal('ground_total', 10, 2)->default(0); 
            $table->enum('status', ['all', 'in-cart', 'setted', 'voided', 'completed'])->default('all');
            $table->boolean('is_active')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

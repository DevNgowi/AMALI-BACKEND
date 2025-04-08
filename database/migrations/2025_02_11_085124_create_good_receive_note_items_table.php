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

        Schema::create('good_receive_note_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grn_id'); 
            $table->foreignId('purchase_order_item_id')->nullable()->constrained('purchase_order_items')->onDelete('cascade');            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->decimal('ordered_quantity', 10, 2);
            $table->decimal('received_quantity', 10, 2);
            $table->decimal('accepted_quantity', 10, 2);
            $table->decimal('rejected_quantity', 10, 2)->default(0);
            $table->decimal('unit_price', 10, 2);
            $table->enum('received_condition', ['Good', 'Damaged', 'Expired'])->default('Good');
            $table->foreign('grn_id')->references('id')->on('good_receipt_notes')->onDelete('cascade');
            $table->timestamps();

        });

    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('good_receive_note_items');
    }
};

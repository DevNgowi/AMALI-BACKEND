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
        Schema::create('good_receipt_note_extra_charges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grn_id');
            $table->unsignedBigInteger('extra_charge_id');
            $table->unsignedBigInteger('unit_id');
            $table->decimal('amount', 10, 2);
            $table->foreign('grn_id')->references('id')->on('good_receipt_notes')->onDelete('cascade');
            $table->foreign('extra_charge_id')->references('id')->on('extra_charges')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('good_receipt_note_extra_charges');
    }
};

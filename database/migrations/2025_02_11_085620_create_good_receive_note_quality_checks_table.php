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
        Schema::create('good_receive_note_quality_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grn_id'); 
            $table->foreignId('grn_item_id')->constrained('good_receive_note_items');
            $table->foreignId('checked_by')->constrained('users');
            $table->dateTime('check_date');
            $table->enum('status', ['pass', 'fail']);
            $table->text('remarks')->nullable();
            $table->foreign('grn_id')->references('id')->on('good_receipt_notes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('good_receive_note_quality_checks');
    }
};

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
        Schema::create('extra_charges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('amount', 10, 2);
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->unsignedBigInteger('charge_type_id');
            $table->foreign('tax_id')->references('id')->on(table: 'taxes')->onUpdate('cascade');
            $table->foreign('charge_type_id')->references('id')->on(table: 'extra_charge_types')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extra_charges');
    }
};

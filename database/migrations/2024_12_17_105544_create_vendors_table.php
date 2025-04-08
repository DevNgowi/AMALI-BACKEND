<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('email', 255)->unique();
            $table->string('phone', 20);
            $table->text('address');
            $table->unsignedBigInteger('city_id');
            $table->string('state', 100);
            $table->string('postal_code', 20)->nullable();
            $table->unsignedBigInteger('country_id');
            $table->string('contact_person', 255)->nullable();
            $table->string('tin', 100)->nullable(); 
            $table->string('vrn', 100)->nullable(); 
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreign('city_id')->references('id')->on('cities')->onUpdate('cascade');
            $table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};

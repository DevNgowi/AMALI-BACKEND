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
        Schema::create('employees', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('first_name'); // Employee first name
            $table->string('last_name'); // Employee last name
            $table->string('email')->unique()->nullable(); // Employee email
            $table->string('phone')->nullable(); // Employee phone number
            $table->unsignedBigInteger('gender_id'); // Gender
            $table->date('date_of_birth')->nullable(); // Date of birth
            $table->text('address')->nullable(); // Address
            $table->unsignedBigInteger('city_id')->nullable(); // Foreign key for city
            $table->unsignedBigInteger('position_id')->nullable(); // Foreign key for position
            $table->decimal('salary', 10, 2)->nullable(); // Employee salary
            $table->date('date_of_joining')->nullable(); // Date of joining
            $table->date('dob')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('status')->default('active'); // Status (e.g., active, terminated)
            $table->timestamps(); // Created and updated timestamps
            $table->softDeletes(); // For soft deletion

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('set null');
            $table->foreign('gender_id')->references('id')->on('genders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

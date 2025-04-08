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
        Schema::create('authors', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Author's first name
            $table->string('email')->unique()->nullable(); // Author's email (optional)
            $table->string('phone')->nullable(); // Author's phone number (optional)
            $table->text('biography')->nullable(); // Author's biography (optional)
            $table->unsignedBigInteger('country_id')->nullable();
            $table->timestamps(); // Created and updated timestamps
            $table->softDeletes(); // For soft deletion

            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};

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
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('tax_type', ['inclusive', 'exclusive'])->default('exclusive');
            $table->enum('tax_mode', ['percentage', 'amount'])->default('percentage');  
            $table->decimal('tax_percentage', 10, 2)->nullable();                     
            $table->decimal('tax_amount', 10, 2)->nullable();                          
            $table->timestamps();                                                     
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};

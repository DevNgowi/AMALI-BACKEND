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
        Schema::create('company_fiscal_years', function (Blueprint $table) {
          
                $table->id();
                $table->foreignId('company_id')
                      ->constrained()
                      ->onDelete('cascade');
                $table->date('financial_year_from');
                $table->date('financial_year_to');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
    
                // Add index for date ranges
                $table->index(['financial_year_from', 'financial_year_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_fiscal_years');
    }
};

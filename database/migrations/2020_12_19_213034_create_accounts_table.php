<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Define account types suitable for selling products
    private const ACCOUNT_TYPES = ['SALES', 'EXPENSE', 'TAX', 'REVENUE', 'DISCOUNT'];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', self::ACCOUNT_TYPES); // Define product sales account types
            $table->string('account_number')->unique();
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->foreignId('currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};

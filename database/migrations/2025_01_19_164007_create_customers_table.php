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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code')->unique();
            $table->string('customer_name');
            $table->foreignId('customer_type_id')->constrained();
            $table->foreignId('city_id')->constrained();
            $table->decimal('opening_balance', 15, 2)->default(0.00);
            $table->enum('dr_cr', ['Dr', 'Cr'])->default('Dr');
            $table->string('mobile_sms_no')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->decimal('credit_limit', 15, 2)->nullable();
            $table->integer('credit_period')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('account_ledger_creation')->default(true);
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

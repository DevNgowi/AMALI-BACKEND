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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->foreignId('country_id')
            ->constrained()
            ->onDelete('cascade');
            $table->string('state')->nullable();
            $table->string('email');
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('post_code')->nullable();
            $table->string('tin_no')->nullable();
            $table->string('vrn_no')->nullable();
            $table->string('company_logo')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Add indexes
            $table->index('company_name');
            $table->index('email');
            $table->index('tin_no');
        });
    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
    

   
};

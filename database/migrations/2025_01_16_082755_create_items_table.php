<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('item_type_id')->nullable();
            $table->unsignedBigInteger('item_group_id')->nullable();
            $table->text('description')->nullable();
            $table->date('exprire_date')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onUpdate('cascade')
                ->onDelete('cascade');
           
            $table->foreign('item_type_id')->references('id')->on('item_types')->onDelete('set null');
            $table->foreign('item_group_id')->references('id')->on('item_groups')->onDelete('set null');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};

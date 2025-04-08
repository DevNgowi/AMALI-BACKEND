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
        Schema::create('printer_settings', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->foreignId('virtual_device_id')->constrained()->onDelete('cascade'); // Foreign key to virtual_devices table
            $table->string('printer_name'); // Name of the printer
            $table->string('printer_ip'); // IP address of the printer
            $table->string('printer_type'); // Type of the printer (e.g., inkjet, laser)
            $table->string('paper_size')->nullable();
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('printer_settings');
    }
};

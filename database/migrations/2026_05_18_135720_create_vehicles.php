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
         Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number')->unique();
            $table->string('brand');
            $table->string('model');
            $table->year('year');
            $table->enum('type', ['angkutan_orang', 'angkutan_barang']);
            $table->enum('ownership', ['milik', 'sewa']);
            $table->string('rental_company')->nullable();
            $table->integer('capacity')->nullable();
            $table->enum('fuel_type', ['bensin', 'solar', 'listrik']);
            $table->integer('current_km')->default(0);
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['available', 'in_use', 'maintenance', 'retired'])->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};

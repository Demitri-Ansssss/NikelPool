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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('employee_id')->nullable()->unique();
            $table->string('license_number')->unique();
            $table->enum('license_type', ['A', 'B1', 'B2', 'C']);
            $table->date('license_expiry');
            $table->string('phone')->nullable();
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['available', 'on_duty', 'off'])->default('available');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};

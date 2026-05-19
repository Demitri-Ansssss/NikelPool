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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->string('purpose');
            $table->string('destination');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('passenger_count')->default(1);
            $table->enum('status', [
                'pending',      // baru dibuat
                'approved',     // disetujui semua level
                'rejected',     // ditolak
                'in_progress',  // sedang digunakan
                'completed',    // selesai
                'cancelled',    // dibatalkan
            ])->default('pending');
            $table->integer('total_km')->nullable();
            $table->decimal('fuel_cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingApproval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingService
{
    /**
     * Proses persetujuan satu level
     */
    public function processApproval(BookingApproval $approval, string $status, ?string $notes = null): bool
    {
        DB::beginTransaction();
        try {
            // Update approval 
            $approval->update([
                'status'       => $status,
                'notes'        => $notes,
                'responded_at' => now(),
            ]);

            $booking = $approval->booking;

            Log::info("Booking {$booking->booking_number}: Level {$approval->level} di-{$status} oleh {$approval->approver->name}");

            if ($status === 'rejected') {
                // Jika ditolak, langsung tolak booking
                $booking->update(['status' => 'rejected']);
                Log::warning("Booking {$booking->booking_number} DITOLAK pada level {$approval->level}");
            } else {
                // Jika disetujui, cek apakah semua level sudah approve
                $pendingCount = $booking->approvals()->where('status', 'pending')->count();

                if ($pendingCount === 0) {
                    // Semua level sudah approve
                    $booking->update(['status' => 'approved']);
                    Log::info("Booking {$booking->booking_number} DISETUJUI PENUH (semua level)");
                } else {
                    Log::info("Booking {$booking->booking_number}: Masih ada {$pendingCount} level pending");
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error processing approval: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mulai perjalanan (ubah status ke in_progress)
     */
    public function startTrip(Booking $booking): bool
    {
        if ($booking->status !== 'approved') return false;

        $booking->update(['status' => 'in_progress']);
        Log::info("Booking {$booking->booking_number}: Perjalanan DIMULAI");
        return true;
    }

    /**
     * Selesaikan perjalanan
     */
    public function completeTrip(Booking $booking, int $totalKm, float $fuelCost): bool
    {
        if ($booking->status !== 'in_progress') return false;

        DB::beginTransaction();
        try {
            $booking->update([
                'status'    => 'completed',
                'total_km'  => $totalKm,
                'fuel_cost' => $fuelCost,
            ]);

            // Update KM kendaraan
            $booking->vehicle->increment('current_km', $totalKm);

            Log::info("Booking {$booking->booking_number}: Perjalanan SELESAI. KM: {$totalKm}, BBM: {$fuelCost}");

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error completing trip: " . $e->getMessage());
            return false;
        }
    }
}
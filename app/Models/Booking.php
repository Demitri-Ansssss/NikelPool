<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Booking extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'booking_number', 'user_id', 'vehicle_id', 'driver_id',
        'purpose', 'destination', 'start_date', 'end_date',
        'passenger_count', 'status', 'total_km', 'fuel_cost', 'notes',
    ];

    protected $casts = [
        'start_date'      => 'datetime',
        'end_date'        => 'datetime',
        'fuel_cost'       => 'decimal:2',
        'passenger_count' => 'integer',
        'total_km'        => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $e) => "Booking {$this->booking_number} di-{$e}");
    }

    // Auto-generate booking number
    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            $booking->booking_number = 'BK-' . date('Ymd') . '-' . str_pad(
                (static::whereDate('created_at', today())->count() + 1), 4, '0', STR_PAD_LEFT
            );
        });

        // Saat booking approved, ubah status kendaraan & driver
        static::updated(function (Booking $booking) {
            if ($booking->isDirty('status')) {
                if ($booking->status === 'in_progress') {
                    $booking->vehicle->update(['status' => 'in_use']);
                    $booking->driver?->update(['status' => 'on_duty']);
                } elseif (in_array($booking->status, ['completed', 'cancelled', 'rejected'])) {
                    $booking->vehicle->update(['status' => 'available']);
                    $booking->driver?->update(['status' => 'available']);
                }
            }
        });
    }

    public function user()     { return $this->belongsTo(User::class); }
    public function vehicle()  { return $this->belongsTo(Vehicle::class); }
    public function driver()   { return $this->belongsTo(Driver::class); }
    public function approvals(){ return $this->hasMany(BookingApproval::class)->orderBy('level'); }

    // Cek apakah semua level sudah approve
    public function isFullyApproved(): bool
    {
        return $this->approvals()->where('status', '!=', 'approved')->doesntExist();
    }

    // Cek apakah ada yang reject
    public function isRejected(): bool
    {
        return $this->approvals()->where('status', 'rejected')->exists();
    }
}
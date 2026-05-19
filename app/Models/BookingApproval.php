<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class BookingApproval extends Model
{
    use LogsActivity;

    protected $fillable = [
        'booking_id', 'approver_id', 'level', 'status', 'notes', 'responded_at',
    ];

    protected $casts = ['responded_at' => 'datetime'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'notes', 'responded_at'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $e) => "Approval level {$this->level} di-{$e}");
    }

    public function booking()  { return $this->belongsTo(Booking::class); }
    public function approver() { return $this->belongsTo(User::class, 'approver_id'); }
}
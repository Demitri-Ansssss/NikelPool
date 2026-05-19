<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Driver extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name', 'employee_id', 'license_number', 'license_type',
        'license_expiry', 'phone', 'region_id', 'status', 'is_active',
    ];

    protected $casts = [
        'license_expiry' => 'date',
        'is_active'      => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function region()   { return $this->belongsTo(Region::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
}   
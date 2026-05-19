<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Vehicle extends Model
{
    use LogsActivity;

    protected $fillable = [
        'plate_number', 'brand', 'model', 'year', 'type', 'ownership',
        'rental_company', 'capacity', 'fuel_type', 'current_km',
        'region_id', 'status', 'notes',
    ];

    protected $casts = ['year' => 'integer', 'current_km' => 'integer'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function region()   { return $this->belongsTo(Region::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function services() { return $this->hasMany(VehicleService::class); }
}
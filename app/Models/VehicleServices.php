<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class VehicleService extends Model
{
    use LogsActivity;

    protected $fillable = [
        'vehicle_id', 'service_date', 'next_service_date',
        'service_type', 'km_at_service', 'cost', 'workshop', 'description',
    ];

    protected $casts = [
        'service_date'      => 'date',
        'next_service_date' => 'date',
        'cost'              => 'decimal:2',
        'km_at_service'     => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function vehicle() { return $this->belongsTo(Vehicle::class); }
}
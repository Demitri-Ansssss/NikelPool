<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Region extends Model
{
    use LogsActivity;

    protected $fillable = ['name', 'type', 'address', 'city', 'province', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function vehicles() { return $this->hasMany(Vehicle::class); }
    public function drivers()  { return $this->hasMany(Driver::class); }
}
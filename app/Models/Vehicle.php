<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_profile_id',
        'brand',
        'model',
        'photo',
    ];

    /**
     * Get the driver profile that owns this vehicle.
     */
    public function driverProfile(): BelongsTo
    {
        return $this->belongsTo(DriverProfile::class);
    }

    /**
     * Get rides that use this vehicle.
     */
    public function rides(): HasMany
    {
        return $this->hasMany(Ride::class);
    }
}

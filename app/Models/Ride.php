<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Ride extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_profile_id',
        'vehicle_id',
        'departure_city_id',
        'arrival_city_id',
        'departure_time',
        'price_per_seat',
        'total_seats',
        'available_seats',
        'meeting_point',
        'notes',
        'admin_note',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'departure_time' => 'datetime',
            'price_per_seat' => 'decimal:2',
            'total_seats' => 'integer',
            'available_seats' => 'integer',
        ];
    }

    public function driverProfile(): BelongsTo
    {
        return $this->belongsTo(DriverProfile::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function departureCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'departure_city_id');
    }

    public function arrivalCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'arrival_city_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(
            Review::class,
            Booking::class,
            'ride_id',
            'booking_id',
            'id',
            'id',
        );
    }
}

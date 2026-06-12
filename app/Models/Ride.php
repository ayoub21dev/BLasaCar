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

    /**
     * Cast ride dates, prices, and seat counts to useful PHP types.
     */
    protected function casts(): array
    {
        return [
            'departure_time' => 'datetime',
            'price_per_seat' => 'decimal:2',
            'total_seats' => 'integer',
            'available_seats' => 'integer',
        ];
    }

    /**
     * Get the driver profile that published the ride.
     */
    public function driverProfile(): BelongsTo
    {
        return $this->belongsTo(DriverProfile::class);
    }

    /**
     * Get the vehicle assigned to the ride.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the city where the ride starts.
     */
    public function departureCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'departure_city_id');
    }

    /**
     * Get the city where the ride ends.
     */
    public function arrivalCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'arrival_city_id');
    }

    /**
     * Get all booking requests for this ride.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get reviews written through bookings on this ride.
     */
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

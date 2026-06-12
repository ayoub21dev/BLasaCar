<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'ride_id',
        'traveler_id',
        'seats_reserved',
        'status',
        'booked_at',
    ];

    /**
     * Cast booking counts and timestamps to useful PHP types.
     */
    protected function casts(): array
    {
        return [
            'seats_reserved' => 'integer',
            'booked_at' => 'datetime',
        ];
    }

    /**
     * Get the ride this traveler booking belongs to.
     */
    public function ride(): BelongsTo
    {
        return $this->belongsTo(Ride::class);
    }

    /**
     * Get the traveler who requested the booking.
     */
    public function traveler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'traveler_id');
    }

    /**
     * Get the review written for this completed booking.
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}

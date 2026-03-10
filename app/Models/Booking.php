<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected function casts(): array
    {
        return [
            'seats_reserved' => 'integer',
            'booked_at' => 'datetime',
        ];
    }

    public function ride(): BelongsTo
    {
        return $this->belongsTo(Ride::class);
    }

    public function traveler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'traveler_id');
    }
}

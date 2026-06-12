<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DriverProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cin_number',
        'cin_photo',
        'cin_front_photo',
        'cin_back_photo',
        'cin_verified',
        'avg_rating',
        'total_trips',
    ];

    /**
     * Cast verification, rating, and trip counts to useful PHP types.
     */
    protected function casts(): array
    {
        return [
            'cin_verified' => 'boolean',
            'avg_rating' => 'decimal:2',
            'total_trips' => 'integer',
        ];
    }

    /**
     * Get the user account that owns this driver profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get vehicles registered to this driver profile.
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Get rides published by this driver profile.
     */
    public function rides(): HasMany
    {
        return $this->hasMany(Ride::class);
    }

    /**
     * Get reviews received by this driver profile.
     */
    public function receivedReviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}

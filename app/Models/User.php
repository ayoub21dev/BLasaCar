<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password_hash',
        'profile_photo',
        'email_verified',
        'phone_verified',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected function casts(): array
    {
        return [
            'email_verified' => 'boolean',
            'phone_verified' => 'boolean',
            'password_hash' => 'hashed',
        ];
    }

    public function driverProfile(): HasOne
    {
        return $this->hasOne(DriverProfile::class);
    }

    public function rides(): HasMany
    {
        return $this->hasMany(Ride::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'traveler_id');
    }

    public function writtenReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function receivedReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewed_user_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}

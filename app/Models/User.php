<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_DRIVER = 'driver';

    public const ROLE_TRAVELER = 'traveler';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password_hash',
        'profile_photo',
        'email_verified',
        'phone_verified',
        'account_status',
        'role',
        'suspended_at',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Cast verification flags, password hashing, and suspension timestamps.
     */
    protected function casts(): array
    {
        return [
            'email_verified' => 'boolean',
            'phone_verified' => 'boolean',
            'password_hash' => 'hashed',
            'suspended_at' => 'datetime',
        ];
    }

    /**
     * Tell Laravel which hashed password value to use for authentication.
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    /**
     * Tell Laravel the database column that stores the password hash.
     */
    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    /**
     * Limit a query to users with a specific role.
     */
    public function scopeRole(Builder $query, string $role): Builder
    {
        return $query->where('role', $role);
    }

    /**
     * Check whether the user can access admin workflows.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check whether the user can publish and manage rides.
     */
    public function isDriver(): bool
    {
        return $this->role === self::ROLE_DRIVER;
    }

    /**
     * Check whether the user can book rides as a traveler.
     */
    public function isTraveler(): bool
    {
        return $this->role === self::ROLE_TRAVELER;
    }

    /**
     * Return the named route for the user's default dashboard.
     */
    public function dashboardRoute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'dashboards.admin',
            self::ROLE_DRIVER => 'dashboards.driver',
            default => 'dashboards.traveler',
        };
    }

    /**
     * Get the user's driver profile when they have become a driver.
     */
    public function driverProfile(): HasOne
    {
        return $this->hasOne(DriverProfile::class);
    }

    /**
     * Get rides published through the user's driver profile.
     */
    public function rides(): HasManyThrough
    {
        return $this->hasManyThrough(
            Ride::class,
            DriverProfile::class,
            'user_id',
            'driver_profile_id',
            'id',
            'id',
        );
    }

    /**
     * Get bookings made by this user as a traveler.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'traveler_id');
    }

    /**
     * Get reviews this user wrote as a traveler.
     */
    public function writtenReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'traveler_id');
    }

    /**
     * Get reviews this user received through their driver profile.
     */
    public function receivedReviews(): HasManyThrough
    {
        return $this->hasManyThrough(
            Review::class,
            DriverProfile::class,
            'user_id',
            'driver_profile_id',
            'id',
            'id',
        );
    }

    /**
     * Get in-app and delivery notifications for this user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get mobile API tokens issued to this user.
     */
    public function mobileApiTokens(): HasMany
    {
        return $this->hasMany(MobileApiToken::class);
    }
}

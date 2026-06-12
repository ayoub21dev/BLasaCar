<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    /**
     * Get rides that depart from this city.
     */
    public function departureRides(): HasMany
    {
        return $this->hasMany(Ride::class, 'departure_city_id');
    }

    /**
     * Get rides that arrive in this city.
     */
    public function arrivalRides(): HasMany
    {
        return $this->hasMany(Ride::class, 'arrival_city_id');
    }
}

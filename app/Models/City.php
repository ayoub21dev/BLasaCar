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

    public function departureRides(): HasMany
    {
        return $this->hasMany(Ride::class, 'departure_city_id');
    }

    public function arrivalRides(): HasMany
    {
        return $this->hasMany(Ride::class, 'arrival_city_id');
    }
}

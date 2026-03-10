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
        'cin_verified',
        'avg_rating',
        'total_trips',
    ];

    protected function casts(): array
    {
        return [
            'cin_verified' => 'boolean',
            'avg_rating' => 'decimal:2',
            'total_trips' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }
}

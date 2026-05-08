<?php

namespace App\Support;

use App\Models\DriverProfile;
use Illuminate\Support\Facades\Storage;

class DriverIdentityPhotos
{
    public const DISK = 'local';

    public const FRONT = 'front';

    public const BACK = 'back';

    public static function path(DriverProfile $profile, string $side): ?string
    {
        return match ($side) {
            self::FRONT => $profile->cin_front_photo ?: $profile->cin_photo,
            self::BACK => $profile->cin_back_photo,
            default => null,
        };
    }

    public static function exists(DriverProfile $profile, string $side): bool
    {
        $path = self::path($profile, $side);

        return filled($path) && Storage::disk(self::DISK)->exists($path);
    }
}

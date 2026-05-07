<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\City;
use App\Models\DriverProfile;
use App\Models\Notification;
use App\Models\Ride;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class InertiaProps
{
    /**
     * @return array{id:int,name:string}
     */
    public static function city(City $city): array
    {
        return [
            'id' => $city->id,
            'name' => $city->name,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function user(User $user, bool $withProfile = false): array
    {
        $payload = [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'name' => trim($user->first_name.' '.$user->last_name),
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'account_status' => $user->account_status,
            'dashboard_route' => $user->dashboardRoute(),
            'initials' => self::initials($user),
            'joined_date' => $user->created_at?->format('d M Y'),
            'email_verified' => (bool) $user->email_verified,
            'phone_verified' => (bool) $user->phone_verified,
            'suspended_at' => $user->suspended_at?->format('d M Y H:i'),
        ];

        if ($withProfile) {
            $payload['driver_profile'] = $user->driverProfile ? self::driverProfile($user->driverProfile) : null;
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    public static function driverProfile(DriverProfile $profile): array
    {
        $frontPath = $profile->cin_front_photo ?: $profile->cin_photo;
        $backPath = $profile->cin_back_photo;
        $frontExists = filled($frontPath) && Storage::disk('public')->exists($frontPath);
        $backExists = filled($backPath) && Storage::disk('public')->exists($backPath);

        return [
            'id' => $profile->id,
            'cin_number' => $profile->cin_number,
            'cin_verified' => (bool) $profile->cin_verified,
            'avg_rating' => number_format((float) $profile->avg_rating, 1),
            'total_trips' => (int) $profile->total_trips,
            'cin_front_photo' => [
                'path' => $frontPath,
                'url' => $frontExists ? Storage::url($frontPath) : null,
                'exists' => $frontExists,
            ],
            'cin_back_photo' => [
                'path' => $backPath,
                'url' => $backExists ? Storage::url($backPath) : null,
                'exists' => $backExists,
            ],
            'vehicle' => $profile->vehicles->first() ? self::vehicle($profile->vehicles->first()) : null,
            'submitted_at' => $profile->created_at?->format('d M Y H:i'),
            'photos_complete' => $frontExists && $backExists,
        ];
    }

    /**
     * @return array{id:int,brand:string,model:string}
     */
    public static function vehicle(Vehicle $vehicle): array
    {
        return [
            'id' => $vehicle->id,
            'brand' => $vehicle->brand,
            'model' => $vehicle->model,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function ride(Ride $ride): array
    {
        $departureTime = $ride->departure_time instanceof Carbon
            ? $ride->departure_time
            : Carbon::parse($ride->departure_time);
        $arrivalTime = $departureTime->copy()->addHours(2)->addMinutes(30);
        $profile = $ride->driverProfile;
        $driver = $profile?->user;

        return [
            'id' => $ride->id,
            'status' => $ride->status,
            'departure_city' => $ride->departureCity ? self::city($ride->departureCity) : null,
            'arrival_city' => $ride->arrivalCity ? self::city($ride->arrivalCity) : null,
            'departure_time' => $departureTime->toIso8601String(),
            'departure_date' => $departureTime->format('Y-m-d'),
            'departure_time_label' => $departureTime->format('H:i'),
            'arrival_time_label' => $arrivalTime->format('H:i'),
            'departure_day_label' => self::dayLabel($departureTime),
            'departure_full_label' => $departureTime->format('l, d M Y'),
            'departure_datetime_label' => $departureTime->format('d M Y \a\t H:i'),
            'price_per_seat' => (float) $ride->price_per_seat,
            'price_label' => number_format((float) $ride->price_per_seat, 0).' DH',
            'total_seats' => (int) $ride->total_seats,
            'available_seats' => (int) $ride->available_seats,
            'available_seats_label' => $ride->available_seats.' '.str('seat')->plural($ride->available_seats).' left',
            'meeting_point' => $ride->meeting_point,
            'notes' => $ride->notes,
            'vehicle' => $ride->vehicle ? self::vehicle($ride->vehicle) : null,
            'driver' => $driver ? [
                ...self::user($driver),
                'profile' => $profile ? [
                    'avg_rating' => number_format((float) $profile->avg_rating, 1),
                    'total_trips' => (int) $profile->total_trips,
                    'cin_verified' => (bool) $profile->cin_verified,
                ] : null,
            ] : null,
            'can_request' => $ride->status === 'scheduled'
                && $ride->available_seats > 0
                && $departureTime->isFuture(),
            'can_complete' => $ride->status === 'scheduled' && $departureTime->lessThanOrEqualTo(now()),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function booking(Booking $booking): array
    {
        return [
            'id' => $booking->id,
            'ride_id' => $booking->ride_id,
            'seats_reserved' => (int) $booking->seats_reserved,
            'status' => $booking->status,
            'ride' => $booking->ride ? self::ride($booking->ride) : null,
            'traveler' => $booking->traveler ? self::user($booking->traveler) : null,
            'can_cancel' => in_array($booking->status, ['pending', 'confirmed'], true)
                && $booking->ride?->departure_time?->isFuture(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function notification(Notification $notification): array
    {
        return [
            'id' => $notification->id,
            'title' => $notification->title,
            'message' => $notification->message,
            'is_read' => (bool) $notification->is_read,
            'created_label' => $notification->created_at?->diffForHumans(),
        ];
    }

    private static function initials(User $user): string
    {
        return strtoupper(str($user->first_name)->substr(0, 1).str($user->last_name)->substr(0, 1));
    }

    private static function dayLabel(Carbon $date): string
    {
        if ($date->isToday()) {
            return 'Today';
        }

        if ($date->isTomorrow()) {
            return 'Tomorrow';
        }

        return $date->format('D, M d');
    }
}

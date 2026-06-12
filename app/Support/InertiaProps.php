<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\City;
use App\Models\DriverProfile;
use App\Models\Notification;
use App\Models\Review;
use App\Models\Ride;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class InertiaProps
{
    /**
     * Convert a city model into a small option payload.
     *
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
     * Convert an authenticated user into dashboard-safe props.
     *
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
            'profile_photo_url' => self::profilePhotoUrl($user),
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
     * Convert a private driver profile into admin/dashboard props.
     *
     * @return array<string, mixed>
     */
    public static function driverProfile(DriverProfile $profile): array
    {
        $frontPath = DriverIdentityPhotos::path($profile, DriverIdentityPhotos::FRONT);
        $backPath = DriverIdentityPhotos::path($profile, DriverIdentityPhotos::BACK);
        $frontExists = DriverIdentityPhotos::exists($profile, DriverIdentityPhotos::FRONT);
        $backExists = DriverIdentityPhotos::exists($profile, DriverIdentityPhotos::BACK);

        return [
            'id' => $profile->id,
            'cin_number' => $profile->cin_number,
            'cin_verified' => (bool) $profile->cin_verified,
            'avg_rating' => number_format((float) $profile->avg_rating, 1),
            'total_trips' => (int) $profile->total_trips,
            'cin_front_photo' => [
                'path' => $frontPath,
                'url' => $frontExists ? route('admin.driver-profiles.cin', [$profile, DriverIdentityPhotos::FRONT]) : null,
                'exists' => $frontExists,
            ],
            'cin_back_photo' => [
                'path' => $backPath,
                'url' => $backExists ? route('admin.driver-profiles.cin', [$profile, DriverIdentityPhotos::BACK]) : null,
                'exists' => $backExists,
            ],
            'vehicle' => $profile->vehicles->first() ? self::vehicle($profile->vehicles->first()) : null,
            'submitted_at' => $profile->created_at?->format('d M Y H:i'),
            'photos_complete' => $frontExists && $backExists,
        ];
    }

    /**
     * Convert a driver profile into public props for traveler-facing pages.
     *
     * @return array<string, mixed>
     */
    public static function publicDriverProfile(DriverProfile $profile): array
    {
        return [
            'id' => $profile->id,
            'cin_verified' => (bool) $profile->cin_verified,
            'avg_rating' => number_format((float) $profile->avg_rating, 1),
            'total_trips' => (int) $profile->total_trips,
            'vehicle' => $profile->vehicles->first() ? self::vehicle($profile->vehicles->first()) : null,
            'vehicles' => $profile->vehicles->map(fn (Vehicle $vehicle) => self::vehicle($vehicle))->values(),
            'submitted_at' => $profile->created_at?->format('d M Y'),
        ];
    }

    /**
     * Convert a vehicle into the small payload used by ride cards and forms.
     *
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
     * Convert a ride into UI props, including derived labels and action flags.
     *
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
                ...self::publicUser($driver),
                'profile' => $profile ? [
                    'id' => $profile->id,
                    'avg_rating' => number_format((float) $profile->avg_rating, 1),
                    'total_trips' => (int) $profile->total_trips,
                    'cin_verified' => (bool) $profile->cin_verified,
                ] : null,
            ] : null,
            'can_request' => $ride->status === 'scheduled'
                && $ride->available_seats > 0
                && $departureTime->isFuture(),
            'can_complete' => $ride->status === 'scheduled' && $departureTime->lessThanOrEqualTo(now()),
            'can_edit' => $ride->status === 'scheduled' && $departureTime->isFuture(),
            'can_cancel' => $ride->status === 'scheduled' && $departureTime->isFuture(),
        ];
    }

    /**
     * Convert a booking into UI props and unlock contact details when allowed.
     *
     * @return array<string, mixed>
     */
    public static function booking(Booking $booking): array
    {
        $driver = $booking->ride?->driverProfile?->user;
        $traveler = $booking->traveler;
        $canViewContact = in_array($booking->status, ['confirmed', 'completed'], true)
            && $traveler?->account_status === 'active'
            && $driver?->account_status === 'active';

        return [
            'id' => $booking->id,
            'ride_id' => $booking->ride_id,
            'seats_reserved' => (int) $booking->seats_reserved,
            'status' => $booking->status,
            'ride' => $booking->ride ? self::ride($booking->ride) : null,
            'traveler' => $traveler ? self::publicUser($traveler) : null,
            'can_view_contact' => $canViewContact,
            'traveler_contact' => $canViewContact && $traveler
                ? self::contact($traveler)
                : null,
            'driver_contact' => $canViewContact && $driver
                ? self::contact($driver)
                : null,
            'can_cancel' => in_array($booking->status, ['pending', 'confirmed'], true)
                && $booking->ride?->departure_time?->isFuture(),
        ];
    }

    /**
     * Convert a notification into the compact dashboard payload.
     *
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

    /**
     * Convert a user into public profile props without private contact fields.
     *
     * @return array<string, mixed>
     */
    public static function profileUser(User $user): array
    {
        return [
            ...self::publicUser($user),
            'role' => $user->role,
            'joined_date' => $user->created_at?->format('d M Y'),
            'email_verified' => (bool) $user->email_verified,
            'phone_verified' => (bool) $user->phone_verified,
        ];
    }

    /**
     * Convert a review into public profile props with route context.
     *
     * @return array<string, mixed>
     */
    public static function review(Review $review): array
    {
        $ride = $review->booking?->ride;

        return [
            'id' => $review->id,
            'rating' => (int) $review->rating,
            'comment' => $review->comment,
            'created_label' => $review->created_at?->format('d M Y'),
            'traveler' => $review->traveler ? self::publicUser($review->traveler) : null,
            'ride' => $ride ? [
                'id' => $ride->id,
                'route' => trim(($ride->departureCity?->name ?? 'Departure').' -> '.($ride->arrivalCity?->name ?? 'Arrival')),
                'departure_datetime_label' => $ride->departure_time?->format('d M Y \a\t H:i'),
            ] : null,
        ];
    }

    /**
     * Build two-letter initials from a user's first and last name.
     */
    private static function initials(User $user): string
    {
        return strtoupper(str($user->first_name)->substr(0, 1).str($user->last_name)->substr(0, 1));
    }

    /**
     * Convert a user into public-safe identity props.
     *
     * @return array<string, mixed>
     */
    public static function publicUser(User $user): array
    {
        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'name' => trim($user->first_name.' '.$user->last_name),
            'initials' => self::initials($user),
            'profile_photo_url' => self::profilePhotoUrl($user),
        ];
    }

    /**
     * Resolve a profile photo from an external URL, public file, or storage disk.
     */
    private static function profilePhotoUrl(User $user): ?string
    {
        if (! $user->profile_photo) {
            return null;
        }

        $path = ltrim($user->profile_photo, '/');

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (is_file(public_path($path))) {
            return '/'.$path;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::url($path);
        }

        return null;
    }

    /**
     * Build the contact payload shown after a booking is accepted.
     *
     * @return array{name:string,phone:string,whatsapp_url:string}
     */
    private static function contact(User $user): array
    {
        $phone = $user->phone ?? '';

        return [
            'name' => trim($user->first_name.' '.$user->last_name) ?: $user->email,
            'phone' => $phone,
            'whatsapp_url' => 'https://wa.me/'.self::whatsAppDigits($phone),
        ];
    }

    /**
     * Normalize a local or international phone number for WhatsApp deep links.
     */
    private static function whatsAppDigits(string $phone): string
    {
        $trimmed = trim($phone);

        if (str_starts_with($trimmed, '+')) {
            return preg_replace('/\D+/', '', $trimmed);
        }

        $digits = preg_replace('/\D+/', '', $trimmed);

        if (str_starts_with($digits, '00')) {
            return substr($digits, 2);
        }

        if (str_starts_with($digits, '0')) {
            return '212'.substr($digits, 1);
        }

        return $digits;
    }

    /**
     * Return a friendly date label for ride cards.
     */
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

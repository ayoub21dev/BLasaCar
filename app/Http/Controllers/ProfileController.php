<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\DriverProfile;
use App\Models\Review;
use App\Models\Ride;
use App\Models\User;
use App\Support\InertiaProps;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Render a public driver profile with reviews and upcoming rides.
     */
    public function showDriver(DriverProfile $driverProfile): Response
    {
        $driverProfile->loadMissing(['user', 'vehicles']);

        abort_unless(
            $driverProfile->user?->isDriver()
            && $driverProfile->user?->account_status === 'active',
            404,
        );

        $reviews = Review::query()
            ->with(['traveler', 'booking.ride.departureCity', 'booking.ride.arrivalCity'])
            ->where('driver_profile_id', $driverProfile->id)
            ->latest()
            ->limit(10)
            ->get();

        $scheduledRides = Ride::query()
            ->with(['driverProfile.user', 'vehicle', 'departureCity', 'arrivalCity'])
            ->where('driver_profile_id', $driverProfile->id)
            ->where('status', 'scheduled')
            ->where('available_seats', '>', 0)
            ->where('departure_time', '>', now())
            ->orderBy('departure_time')
            ->limit(4)
            ->get();

        return Inertia::render('Profiles/Driver', [
            'driver' => InertiaProps::profileUser($driverProfile->user),
            'profile' => InertiaProps::publicDriverProfile($driverProfile),
            'stats' => [
                'review_count' => Review::query()->where('driver_profile_id', $driverProfile->id)->count(),
                'scheduled_rides' => Ride::query()->where('driver_profile_id', $driverProfile->id)->where('status', 'scheduled')->where('departure_time', '>', now())->count(),
                'completed_rides' => Ride::query()->where('driver_profile_id', $driverProfile->id)->where('status', 'completed')->count(),
            ],
            'reviews' => $reviews->map(fn (Review $review) => InertiaProps::review($review))->values(),
            'rides' => $scheduledRides->map(fn (Ride $ride) => InertiaProps::ride($ride))->values(),
        ]);
    }

    /**
     * Render a traveler profile only for drivers who have bookings with that traveler.
     */
    public function showTraveler(Request $request, User $traveler): Response
    {
        $driver = $request->user();
        $driver?->loadMissing('driverProfile');

        abort_unless(
            $driver?->driverProfile
            && $traveler->isTraveler()
            && $traveler->account_status === 'active',
            404,
        );

        $bookings = Booking::query()
            ->with([
                'traveler',
                'ride.driverProfile.user',
                'ride.vehicle',
                'ride.departureCity',
                'ride.arrivalCity',
            ])
            ->where('traveler_id', $traveler->id)
            ->whereHas('ride.driverProfile', fn ($query) => $query->where('user_id', $driver->id))
            ->orderByDesc('booked_at')
            ->get();

        abort_if($bookings->isEmpty(), 404);

        $reviews = Review::query()
            ->with(['traveler', 'booking.ride.departureCity', 'booking.ride.arrivalCity'])
            ->where('traveler_id', $traveler->id)
            ->where('driver_profile_id', $driver->driverProfile->id)
            ->latest()
            ->limit(5)
            ->get();

        return Inertia::render('Profiles/Traveler', [
            'traveler' => InertiaProps::profileUser($traveler),
            'stats' => [
                'total_bookings' => $bookings->count(),
                'active_bookings' => $bookings->whereIn('status', ['pending', 'confirmed'])->count(),
                'completed_trips' => $bookings->where('status', 'completed')->count(),
                'cancelled_trips' => $bookings->where('status', 'cancelled')->count(),
                'seats_reserved' => $bookings->sum('seats_reserved'),
            ],
            'bookings' => $bookings->map(fn (Booking $booking) => InertiaProps::booking($booking))->values(),
            'reviews' => $reviews->map(fn (Review $review) => InertiaProps::review($review))->values(),
        ]);
    }
}

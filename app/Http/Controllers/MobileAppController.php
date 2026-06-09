<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\City;
use App\Models\Review;
use App\Models\Ride;
use App\Services\PublicApp\PublicRideService;
use App\Support\InertiaProps;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class MobileAppController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Mobile/Onboarding');
    }

    public function login(): Response
    {
        return Inertia::render('Mobile/Login');
    }

    public function signup(): Response
    {
        return Inertia::render('Mobile/Signup');
    }

    public function home(PublicRideService $publicRideService): Response
    {
        $this->seedNativeDatabaseIfEmpty();

        $featuredRides = $publicRideService->listBookableRides(6);

        return Inertia::render('Mobile/Home', [
            'cities' => $this->cities(),
            'featuredRides' => $featuredRides->map(fn (Ride $ride) => InertiaProps::ride($ride))->values(),
            'today' => today()->format('Y-m-d'),
        ]);
    }

    public function search(Request $request, PublicRideService $publicRideService): Response
    {
        $this->seedNativeDatabaseIfEmpty();

        $filters = $request->validate([
            'departure_city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'arrival_city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'departure_date' => ['nullable', 'date'],
            'seats' => ['nullable', 'integer', 'min:1', 'max:4'],
        ]);

        $rides = collect();

        if (! empty($filters['departure_city_id']) && ! empty($filters['arrival_city_id'])) {
            $rides = $publicRideService->searchRides(
                (int) $filters['departure_city_id'],
                (int) $filters['arrival_city_id'],
                ! empty($filters['departure_date']) ? Carbon::parse($filters['departure_date']) : null,
            );
        } else {
            $rides = $publicRideService->listBookableRides(12);
        }

        if (! empty($filters['seats'])) {
            $rides = $rides
                ->filter(fn (Ride $ride) => $ride->available_seats >= (int) $filters['seats'])
                ->values();
        }

        return Inertia::render('Mobile/Search', [
            'cities' => $this->cities(),
            'rides' => $rides->map(fn (Ride $ride) => InertiaProps::ride($ride))->values(),
            'filters' => $filters,
        ]);
    }

    public function ride(Ride $ride, PublicRideService $publicRideService): Response
    {
        $this->seedNativeDatabaseIfEmpty();

        abort_unless($publicRideService->canViewRideDetails(auth()->user(), $ride), 404);

        return Inertia::render('Mobile/RideDetails', [
            'ride' => InertiaProps::ride($publicRideService->getRideDetails($ride)),
        ]);
    }

    public function trips(PublicRideService $publicRideService): Response
    {
        $this->seedNativeDatabaseIfEmpty();

        $user = auth()->user();
        $bookings = collect();

        if ($user?->isTraveler()) {
            $bookings = $publicRideService->listBookingStatuses($user);
        }

        $upcomingBookings = $bookings->filter(function (Booking $booking): bool {
            return in_array($booking->status, ['pending', 'confirmed'], true)
                && $booking->ride?->departure_time?->isFuture();
        })->values();

        $reviewedBookingIds = $user
            ? Review::query()
                ->where('traveler_id', $user->id)
                ->whereIn('booking_id', $bookings->pluck('id'))
                ->pluck('booking_id')
            : collect();

        return Inertia::render('Mobile/Trips', [
            'bookings' => $bookings->map(function (Booking $booking) use ($reviewedBookingIds) {
                return [
                    ...InertiaProps::booking($booking),
                    'reviewed' => $reviewedBookingIds->contains($booking->id),
                    'can_review' => $booking->status === 'completed' && ! $reviewedBookingIds->contains($booking->id),
                ];
            })->values(),
            'upcomingBookings' => $upcomingBookings->map(fn (Booking $booking) => InertiaProps::booking($booking))->values(),
        ]);
    }

    public function account(PublicRideService $publicRideService): Response
    {
        $this->seedNativeDatabaseIfEmpty();

        $user = auth()->user();
        $bookings = collect();
        $notifications = collect();

        if ($user?->isTraveler()) {
            $bookings = $publicRideService->listBookingStatuses($user);
        }

        if ($user !== null) {
            $notifications = $user->notifications()
                ->orderByDesc('created_at')
                ->limit(6)
                ->get();
        }

        return Inertia::render('Mobile/Account', [
            'bookings' => $bookings->map(fn (Booking $booking) => InertiaProps::booking($booking))->values(),
            'notifications' => $notifications->map(fn ($notification) => InertiaProps::notification($notification))->values(),
            'stats' => [
                'upcoming_trips' => $bookings
                    ->filter(fn (Booking $booking) => in_array($booking->status, ['pending', 'confirmed'], true)
                        && $booking->ride?->departure_time?->isFuture())
                    ->count(),
                'completed_trips' => $bookings->where('status', 'completed')->count(),
                'cancelled_trips' => $bookings->where('status', 'cancelled')->count(),
            ],
        ]);
    }

    private function cities()
    {
        return City::query()
            ->orderBy('name')
            ->get()
            ->map(fn (City $city) => InertiaProps::city($city))
            ->values();
    }

    private function seedNativeDatabaseIfEmpty(): void
    {
        if (! config('nativephp-internal.running') || City::query()->exists()) {
            return;
        }

        app(DatabaseSeeder::class)->run();
    }
}

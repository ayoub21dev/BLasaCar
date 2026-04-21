<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\City;
use App\Models\Ride;
use App\Models\User;
use App\Services\Admin\AdminService;
use App\Services\PublicApp\PublicRideService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class FrontendController extends Controller
{
    public function home(): View
    {
        $cities = City::query()->orderBy('name')->get();

        $featuredRides = $this->bookableRidesQuery()
            ->limit(4)
            ->get();

        return view('pages.home', [
            'cities' => $cities,
            'featuredRides' => $featuredRides,
        ]);
    }

    public function search(Request $request, PublicRideService $publicRideService): View
    {
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
            $rides = $this->bookableRidesQuery()
                ->limit(12)
                ->get();
        }

        if (! empty($filters['seats'])) {
            $rides = $rides
                ->filter(fn (Ride $ride) => $ride->available_seats >= (int) $filters['seats'])
                ->values();
        }

        return view('pages.search', [
            'cities' => City::query()->orderBy('name')->get(),
            'rides' => $rides,
            'filters' => $filters,
        ]);
    }

    public function showRide(Ride $ride, PublicRideService $publicRideService): View
    {
        return view('pages.ride-details', [
            'ride' => $publicRideService->getRideDetails($ride),
        ]);
    }

    public function publishRide(): View
    {
        return view('pages.publish', [
            'cities' => City::query()->orderBy('name')->get(),
        ]);
    }

    public function login(): View
    {
        return view('pages.auth.login');
    }

    public function signup(): View
    {
        return view('pages.auth.signup');
    }

    public function adminDashboard(AdminService $adminService): View
    {
        $users = $adminService->listUsers();
        $rides = $adminService->listRides();

        return view('pages.dashboards.admin', [
            'metrics' => $adminService->dashboardMetrics(),
            'users' => $users->take(6),
            'rides' => $rides->take(6),
            'alerts' => [
                'suspended_users' => $users->where('account_status', 'suspended')->count(),
                'cancelled_rides' => $rides->where('status', 'cancelled')->count(),
            ],
        ]);
    }

    public function driverDashboard(): View
    {
        $driver = User::query()
            ->with([
                'driverProfile',
                'rides.departureCity',
                'rides.arrivalCity',
                'rides.vehicle',
            ])
            ->whereKey(auth()->id())
            ->role(User::ROLE_DRIVER)
            ->firstOrFail();

        $rides = $driver->rides
            ->sortBy('departure_time')
            ->values();

        $bookings = Booking::query()
            ->with([
                'traveler',
                'ride.departureCity',
                'ride.arrivalCity',
            ])
            ->whereHas('ride', fn ($query) => $query->where('user_id', $driver->id))
            ->orderByDesc('booked_at')
            ->get();

        $publishedRides = $rides->count();
        $completedRides = $rides->where('status', 'completed')->count();
        $completionRate = $publishedRides > 0
            ? (int) round(($completedRides / $publishedRides) * 100)
            : 0;

        $handledBookings = $bookings->whereIn('status', ['confirmed', 'completed', 'cancelled'])->count();
        $responseRate = $bookings->count() > 0
            ? (int) round(($handledBookings / $bookings->count()) * 100)
            : 100;

        return view('pages.dashboards.driver', [
            'driver' => $driver,
            'rides' => $rides,
            'bookings' => $bookings,
            'stats' => [
                'published_rides' => $publishedRides,
                'upcoming_rides' => $rides->where('status', 'scheduled')->count(),
                'completion_rate' => $completionRate,
                'response_rate' => $responseRate,
            ],
            'weeklySeatSales' => $this->weeklySeatSales($bookings),
        ]);
    }

    public function travelerDashboard(PublicRideService $publicRideService): View
    {
        $traveler = User::query()
            ->with([
                'bookings.ride.user.driverProfile',
                'bookings.ride.departureCity',
                'bookings.ride.arrivalCity',
            ])
            ->whereKey(auth()->id())
            ->role(User::ROLE_TRAVELER)
            ->firstOrFail();

        $bookings = $publicRideService->listBookingStatuses($traveler);
        $upcomingBookings = $bookings->filter(function (Booking $booking): bool {
            return in_array($booking->status, ['pending', 'confirmed'], true)
                && $booking->ride?->departure_time?->isFuture();
        })->values();

        $averageDriverRating = $bookings
            ->filter(fn (Booking $booking) => $booking->ride?->user?->driverProfile !== null)
            ->avg(fn (Booking $booking) => (float) $booking->ride->user->driverProfile->avg_rating);

        return view('pages.dashboards.traveler', [
            'traveler' => $traveler,
            'bookings' => $bookings,
            'upcomingBookings' => $upcomingBookings,
            'stats' => [
                'upcoming_trips' => $upcomingBookings->count(),
                'completed_trips' => $bookings->where('status', 'completed')->count(),
                'cancelled_trips' => $bookings->where('status', 'cancelled')->count(),
                'avg_driver_rating' => $averageDriverRating ? number_format($averageDriverRating, 1) : '0.0',
            ],
        ]);
    }

    private function bookableRidesQuery()
    {
        return Ride::query()
            ->with([
                'user.driverProfile',
                'vehicle',
                'departureCity',
                'arrivalCity',
            ])
            ->where('status', 'scheduled')
            ->where('available_seats', '>', 0)
            ->whereHas('user', fn ($query) => $query->where('account_status', 'active'))
            ->orderBy('departure_time');
    }

    private function weeklySeatSales($bookings): array
    {
        $countsByDay = $bookings
            ->groupBy(fn (Booking $booking) => $booking->booked_at->format('Y-m-d'))
            ->map(fn ($group) => $group->sum('seats_reserved'));

        $series = collect(range(6, 0))->map(function (int $offset) use ($countsByDay) {
            $day = now()->subDays($offset);
            $key = $day->format('Y-m-d');

            return [
                'label' => $day->format('D'),
                'seats' => $countsByDay->get($key, 0),
            ];
        });

        $maxSeats = max(1, (int) $series->max('seats'));

        return $series
            ->map(fn (array $day) => [
                ...$day,
                'height' => max(18, (int) round(($day['seats'] / $maxSeats) * 100)),
            ])
            ->all();
    }
}

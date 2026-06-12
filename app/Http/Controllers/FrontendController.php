<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\City;
use App\Models\Review;
use App\Models\Ride;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\Admin\AdminService;
use App\Services\PublicApp\PublicRideService;
use App\Support\InertiaProps;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class FrontendController extends Controller
{
    /**
     * Render the public homepage with cities and a few upcoming rides.
     */
    public function home(): Response
    {
        $cities = City::query()->orderBy('name')->get();

        $featuredRides = $this->bookableRidesQuery()
            ->limit(5)
            ->get();

        return Inertia::render('Home', [
            'cities' => $cities->map(fn (City $city) => InertiaProps::city($city))->values(),
            'featuredRides' => $featuredRides->map(fn (Ride $ride) => InertiaProps::ride($ride))->values(),
            'today' => today()->format('Y-m-d'),
        ]);
    }

    /**
     * Render search results using route, date, and seat filters.
     */
    public function search(Request $request, PublicRideService $publicRideService): Response
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

        return Inertia::render('Search', [
            'cities' => City::query()->orderBy('name')->get()->map(fn (City $city) => InertiaProps::city($city))->values(),
            'rides' => $rides->map(fn (Ride $ride) => InertiaProps::ride($ride))->values(),
            'filters' => $filters,
        ]);
    }

    /**
     * Render a ride details page when the viewer is allowed to see it.
     */
    public function showRide(Request $request, Ride $ride, PublicRideService $publicRideService): Response
    {
        abort_unless($publicRideService->canViewRideDetails($request->user(), $ride), 404);

        return Inertia::render('RideDetails', [
            'ride' => InertiaProps::ride($publicRideService->getRideDetails($ride)),
        ]);
    }

    /**
     * Render the publish form with the driver's vehicles and verification status.
     */
    public function publishRide(): Response
    {
        $vehicles = collect();
        $user = auth()->user();

        if ($user?->isDriver()) {
            $vehicles = Vehicle::query()
                ->where('driver_profile_id', $user->driverProfile?->id)
                ->orderBy('brand')
                ->orderBy('model')
                ->get();
        }

        return Inertia::render('Publish', [
            'cities' => City::query()->orderBy('name')->get()->map(fn (City $city) => InertiaProps::city($city))->values(),
            'canPublishRide' => $user?->isDriver() && $user->driverProfile?->cin_verified === true,
            'verificationPending' => $user?->isDriver() && $user->driverProfile?->cin_verified !== true,
            'vehicles' => $vehicles->map(fn (Vehicle $vehicle) => InertiaProps::vehicle($vehicle))->values(),
        ]);
    }

    /**
     * Render the ride edit form for the owning verified driver.
     */
    public function editRide(Ride $ride): Response
    {
        $user = auth()->user();
        $ride->loadMissing(['driverProfile', 'vehicle', 'departureCity', 'arrivalCity']);

        abort_unless(
            $user?->isDriver()
            && $user->driverProfile?->cin_verified === true
            && $ride->driverProfile?->user_id === $user->id
            && $ride->status === 'scheduled'
            && $ride->departure_time->isFuture(),
            403,
        );

        $vehicles = Vehicle::query()
            ->where('driver_profile_id', $user->driverProfile?->id)
            ->orderBy('brand')
            ->orderBy('model')
            ->get();

        return Inertia::render('Publish', [
            'cities' => City::query()->orderBy('name')->get()->map(fn (City $city) => InertiaProps::city($city))->values(),
            'canPublishRide' => true,
            'verificationPending' => false,
            'vehicles' => $vehicles->map(fn (Vehicle $vehicle) => InertiaProps::vehicle($vehicle))->values(),
            'mode' => 'edit',
            'ride' => InertiaProps::ride($ride),
        ]);
    }

    /**
     * Render the desktop login page.
     */
    public function login(): Response
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Render the desktop signup page.
     */
    public function signup(): Response
    {
        return Inertia::render('Auth/Signup');
    }

    /**
     * Render the admin dashboard overview section.
     */
    public function adminDashboard(AdminService $adminService): Response
    {
        return $this->adminDashboardView($adminService, 'overview');
    }

    /**
     * Render the admin section for pending driver verification.
     */
    public function adminDriverVerification(AdminService $adminService): Response
    {
        return $this->adminDashboardView($adminService, 'driver-verification');
    }

    /**
     * Render the admin user management section.
     */
    public function adminUsers(AdminService $adminService): Response
    {
        return $this->adminDashboardView($adminService, 'users');
    }

    /**
     * Render the admin ride activity section.
     */
    public function adminRideActivity(AdminService $adminService): Response
    {
        return $this->adminDashboardView($adminService, 'rides');
    }

    /**
     * Build the shared data payload for all admin dashboard sections.
     */
    private function adminDashboardView(AdminService $adminService, string $section): Response
    {
        $users = $adminService->listUsers();
        $rides = $adminService->listRides();

        return Inertia::render('Dashboards/Admin', [
            'section' => $section,
            'metrics' => $adminService->dashboardMetrics(),
            'users' => $users->map(fn (User $user) => InertiaProps::user($user, true))->values(),
            'rides' => ($section === 'rides' ? $rides : $rides->take(6))
                ->map(fn (Ride $ride) => InertiaProps::ride($ride))
                ->values(),
            'pendingDriverProfiles' => $adminService->listPendingDriverVerifications()
                ->map(fn ($profile) => [
                    ...InertiaProps::driverProfile($profile),
                    'user' => $profile->user ? InertiaProps::user($profile->user) : null,
                ])
                ->values(),
            'alerts' => [
                'suspended_users' => $users->where('account_status', 'suspended')->count(),
                'cancelled_rides' => $rides->where('status', 'cancelled')->count(),
            ],
        ]);
    }

    /**
     * Render a driver's dashboard with rides, booking requests, and stats.
     */
    public function driverDashboard(): Response
    {
        $driver = User::query()
            ->with([
                'driverProfile',
                'rides.departureCity',
                'rides.arrivalCity',
                'rides.vehicle',
                'rides.driverProfile.user',
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
                'ride.driverProfile.user',
                'ride.vehicle',
                'ride.departureCity',
                'ride.arrivalCity',
            ])
            ->whereHas('ride.driverProfile', fn ($query) => $query->where('user_id', $driver->id))
            ->orderByDesc('booked_at')
            ->get();

        $publishedRides = $rides->count();
        $completedRides = $rides->where('status', 'completed')->count();
        $completionRate = $publishedRides > 0
            ? (int) round(($completedRides / $publishedRides) * 100)
            : 0;

        $handledBookings = $bookings->whereIn('status', ['confirmed', 'rejected', 'completed', 'cancelled'])->count();
        $responseRate = $bookings->count() > 0
            ? (int) round(($handledBookings / $bookings->count()) * 100)
            : 100;

        return Inertia::render('Dashboards/Driver', [
            'driver' => InertiaProps::user($driver),
            'rides' => $rides->map(fn (Ride $ride) => InertiaProps::ride($ride))->values(),
            'bookings' => $bookings->map(fn (Booking $booking) => InertiaProps::booking($booking))->values(),
            'notifications' => $driver->notifications()
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
                ->map(fn ($notification) => InertiaProps::notification($notification))
                ->values(),
            'stats' => [
                'published_rides' => $publishedRides,
                'upcoming_rides' => $rides->where('status', 'scheduled')->count(),
                'completion_rate' => $completionRate,
                'response_rate' => $responseRate,
            ],
            'weeklySeatSales' => $this->weeklySeatSales($bookings),
        ]);
    }

    /**
     * Render a traveler's dashboard with bookings, upcoming trips, reviews, and stats.
     */
    public function travelerDashboard(PublicRideService $publicRideService): Response
    {
        $traveler = User::query()
            ->with([
                'bookings.ride.driverProfile.user',
                'bookings.ride.vehicle',
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
            ->filter(fn (Booking $booking) => $booking->ride?->driverProfile !== null)
            ->avg(fn (Booking $booking) => (float) $booking->ride->driverProfile->avg_rating);

        $reviewedBookingIds = Review::query()
            ->where('traveler_id', $traveler->id)
            ->whereIn('booking_id', $bookings->pluck('id'))
            ->pluck('booking_id');

        return Inertia::render('Dashboards/Traveler', [
            'traveler' => InertiaProps::user($traveler),
            'bookings' => $bookings->map(function (Booking $booking) use ($reviewedBookingIds) {
                return [
                    ...InertiaProps::booking($booking),
                    'reviewed' => $reviewedBookingIds->contains($booking->id),
                    'can_review' => $booking->status === 'completed' && ! $reviewedBookingIds->contains($booking->id),
                ];
            })->values(),
            'upcomingBookings' => $upcomingBookings->map(fn (Booking $booking) => InertiaProps::booking($booking))->values(),
            'notifications' => $traveler->notifications()
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
                ->map(fn ($notification) => InertiaProps::notification($notification))
                ->values(),
            'stats' => [
                'upcoming_trips' => $upcomingBookings->count(),
                'completed_trips' => $bookings->where('status', 'completed')->count(),
                'cancelled_trips' => $bookings->where('status', 'cancelled')->count(),
                'avg_driver_rating' => $averageDriverRating ? number_format($averageDriverRating, 1) : '0.0',
            ],
        ]);
    }

    /**
     * Build the shared query for visible, future rides with open seats.
     */
    private function bookableRidesQuery()
    {
        return Ride::query()
            ->with([
                'driverProfile.user',
                'vehicle',
                'departureCity',
                'arrivalCity',
            ])
            ->where('status', 'scheduled')
            ->where('available_seats', '>', 0)
            ->where('departure_time', '>', now())
            ->whereHas('driverProfile.user', fn ($query) => $query->where('account_status', 'active'))
            ->orderBy('departure_time');
    }

    /**
     * Convert recent bookings into a seven-day seat-count chart series.
     */
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

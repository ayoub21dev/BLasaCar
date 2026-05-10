<?php

namespace App\Services\PublicApp;

use App\Models\Booking;
use App\Models\Ride;
use App\Models\User;
use App\Services\Notifications\BookingNotificationService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class PublicRideService
{
    public function __construct(
        private readonly BookingNotificationService $notifications,
    ) {}

    /**
     * @return Collection<int, Ride>
     */
    public function searchRides(int $departureCityId, int $arrivalCityId, ?Carbon $departureDate = null): Collection
    {
        $query = Ride::query()
            ->with([
                'driverProfile.user',
                'vehicle',
                'departureCity',
                'arrivalCity',
            ])
            ->where('departure_city_id', $departureCityId)
            ->where('arrival_city_id', $arrivalCityId)
            ->where('status', 'scheduled')
            ->where('available_seats', '>', 0)
            ->where('departure_time', '>', now())
            ->whereHas('driverProfile.user', fn ($builder) => $builder->where('account_status', 'active'))
            ->orderBy('departure_time');

        if ($departureDate !== null) {
            $query
                ->where('departure_time', '>=', $departureDate->copy()->startOfDay())
                ->where('departure_time', '<', $departureDate->copy()->addDay()->startOfDay());
        }

        return $query->get();
    }

    public function getRideDetails(Ride $ride): Ride
    {
        return $ride->load([
            'driverProfile.user',
            'vehicle',
            'departureCity',
            'arrivalCity',
            'bookings.traveler',
        ]);
    }

    public function canViewRideDetails(?User $viewer, Ride $ride): bool
    {
        $ride->loadMissing('driverProfile.user');

        if ($this->isPubliclyVisible($ride)) {
            return true;
        }

        if ($viewer === null || $viewer->account_status !== 'active') {
            return false;
        }

        if ($viewer->isAdmin()) {
            return true;
        }

        if ($ride->driverProfile?->user_id === $viewer->id) {
            return true;
        }

        return Booking::query()
            ->where('ride_id', $ride->id)
            ->where('traveler_id', $viewer->id)
            ->exists();
    }

    public function requestSeat(User $traveler, Ride $ride, int $seatsRequested): Booking
    {
        if ($seatsRequested < 1) {
            throw new InvalidArgumentException('At least one seat must be requested.');
        }

        return DB::transaction(function () use ($traveler, $ride, $seatsRequested): Booking {
            /** @var Ride $lockedRide */
            $lockedRide = Ride::query()
                ->whereKey($ride->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $this->assertRideCanBeBooked($traveler, $lockedRide, $seatsRequested);
            $this->assertTravelerHasNoActiveBooking($traveler, $lockedRide);

            $booking = Booking::query()->create([
                'ride_id' => $lockedRide->id,
                'traveler_id' => $traveler->id,
                'seats_reserved' => $seatsRequested,
                'status' => 'pending',
                'booked_at' => now(),
            ]);

            $lockedRide->decrement('available_seats', $seatsRequested);

            $booking = $booking->fresh(['ride.driverProfile.user', 'ride.departureCity', 'ride.arrivalCity', 'traveler']);
            $this->notifications->bookingRequested($booking);

            return $booking;
        });
    }

    /**
     * @return Collection<int, Booking>
     */
    public function listBookingStatuses(User $traveler): Collection
    {
        return Booking::query()
            ->with([
                'ride.driverProfile.user',
                'ride.departureCity',
                'ride.arrivalCity',
            ])
            ->where('traveler_id', $traveler->id)
            ->orderByDesc('booked_at')
            ->get();
    }

    public function cancelBooking(Booking $booking): Booking
    {
        if (! in_array($booking->status, ['pending', 'confirmed'], true)) {
            throw new RuntimeException('Only pending or confirmed bookings can be cancelled.');
        }

        return DB::transaction(function () use ($booking): Booking {
            /** @var Booking $lockedBooking */
            $lockedBooking = Booking::query()
                ->whereKey($booking->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (! in_array($lockedBooking->status, ['pending', 'confirmed'], true)) {
                throw new RuntimeException('Only pending or confirmed bookings can be cancelled.');
            }

            /** @var Ride $ride */
            $ride = Ride::query()
                ->whereKey($lockedBooking->ride_id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedBooking->update([
                'status' => 'cancelled',
            ]);

            $ride->increment('available_seats', $lockedBooking->seats_reserved);

            $lockedBooking = $lockedBooking->fresh(['ride.driverProfile.user', 'ride.departureCity', 'ride.arrivalCity', 'traveler']);
            $this->notifications->bookingCancelledByTraveler($lockedBooking);

            return $lockedBooking;
        });
    }

    public function confirmBooking(User $driver, Booking $booking): Booking
    {
        return DB::transaction(function () use ($driver, $booking): Booking {
            /** @var Booking $lockedBooking */
            $lockedBooking = Booking::query()
                ->with('ride.driverProfile')
                ->whereKey($booking->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $this->assertDriverCanHandleBooking($driver, $lockedBooking);

            $lockedBooking->update([
                'status' => 'confirmed',
            ]);

            $lockedBooking = $lockedBooking->fresh(['ride.driverProfile.user', 'ride.departureCity', 'ride.arrivalCity', 'traveler']);
            $this->notifications->bookingConfirmed($lockedBooking);

            return $lockedBooking;
        });
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function updateRide(User $driver, Ride $ride, array $validated): Ride
    {
        return DB::transaction(function () use ($driver, $ride, $validated): Ride {
            /** @var Ride $lockedRide */
            $lockedRide = Ride::query()
                ->with('driverProfile')
                ->whereKey($ride->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $this->assertDriverCanEditRide($driver, $lockedRide);

            $reservedSeats = (int) Booking::query()
                ->where('ride_id', $lockedRide->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->sum('seats_reserved');

            $totalSeats = (int) $validated['seats_offered'];

            if ($reservedSeats > $totalSeats) {
                throw new RuntimeException('Seats offered cannot be lower than currently reserved seats.');
            }

            $lockedRide->update([
                'vehicle_id' => $validated['vehicle_id'],
                'departure_city_id' => $validated['departure_city_id'],
                'arrival_city_id' => $validated['arrival_city_id'],
                'departure_time' => Carbon::parse($validated['departure_date'].' '.$validated['departure_time']),
                'price_per_seat' => $validated['price_per_seat'],
                'total_seats' => $totalSeats,
                'available_seats' => $totalSeats - $reservedSeats,
                'meeting_point' => $validated['meeting_point'],
                'notes' => $validated['notes'] ?? null,
            ]);

            return $lockedRide->fresh(['driverProfile.user', 'vehicle', 'departureCity', 'arrivalCity']);
        });
    }

    public function cancelRide(User $driver, Ride $ride): Ride
    {
        return DB::transaction(function () use ($driver, $ride): Ride {
            /** @var Ride $lockedRide */
            $lockedRide = Ride::query()
                ->with('driverProfile')
                ->whereKey($ride->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $this->assertDriverCanCancelRide($driver, $lockedRide);

            $activeBookings = Booking::query()
                ->with(['ride.driverProfile.user', 'ride.departureCity', 'ride.arrivalCity', 'traveler'])
                ->where('ride_id', $lockedRide->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->lockForUpdate()
                ->get();

            $lockedRide->update([
                'status' => 'cancelled',
                'available_seats' => 0,
            ]);

            foreach ($activeBookings as $booking) {
                $booking->update(['status' => 'cancelled']);
                $this->notifications->rideCancelledByDriver($booking->fresh(['ride.driverProfile.user', 'ride.departureCity', 'ride.arrivalCity', 'traveler']));
            }

            return $lockedRide->fresh(['bookings.traveler', 'departureCity', 'arrivalCity', 'driverProfile.user']);
        });
    }

    public function rejectBooking(User $driver, Booking $booking): Booking
    {
        return DB::transaction(function () use ($driver, $booking): Booking {
            /** @var Booking $lockedBooking */
            $lockedBooking = Booking::query()
                ->with('ride.driverProfile')
                ->whereKey($booking->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $this->assertDriverCanHandleBooking($driver, $lockedBooking);

            /** @var Ride $ride */
            $ride = Ride::query()
                ->whereKey($lockedBooking->ride_id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedBooking->update([
                'status' => 'rejected',
            ]);

            $ride->increment('available_seats', $lockedBooking->seats_reserved);

            $lockedBooking = $lockedBooking->fresh(['ride.driverProfile.user', 'ride.departureCity', 'ride.arrivalCity', 'traveler']);
            $this->notifications->bookingRejected($lockedBooking);

            return $lockedBooking;
        });
    }

    public function completeRide(User $driver, Ride $ride): Ride
    {
        return DB::transaction(function () use ($driver, $ride): Ride {
            /** @var Ride $lockedRide */
            $lockedRide = Ride::query()
                ->with([
                    'bookings.traveler',
                    'departureCity',
                    'arrivalCity',
                    'driverProfile.user',
                ])
                ->whereKey($ride->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $this->assertDriverCanCompleteRide($driver, $lockedRide);

            $lockedRide->update([
                'status' => 'completed',
                'available_seats' => 0,
            ]);

            $lockedRide->bookings()
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);

            $lockedRide->bookings()
                ->where('status', 'confirmed')
                ->update(['status' => 'completed']);

            $lockedRide->driverProfile?->increment('total_trips');

            $completedBookings = $lockedRide->bookings()
                ->with(['ride.driverProfile.user', 'ride.departureCity', 'ride.arrivalCity', 'traveler'])
                ->where('status', 'completed')
                ->get();

            foreach ($completedBookings as $booking) {
                $this->notifications->rideCompleted($booking);
            }

            return $lockedRide->fresh(['bookings.traveler', 'departureCity', 'arrivalCity', 'driverProfile.user']);
        });
    }

    private function assertRideCanBeBooked(User $traveler, Ride $ride, int $seatsRequested): void
    {
        if ($traveler->account_status !== 'active') {
            throw new RuntimeException('Suspended users cannot book rides.');
        }

        if ($ride->driverProfile?->user_id === $traveler->id) {
            throw new RuntimeException('Drivers cannot book their own rides.');
        }

        if ($ride->status !== 'scheduled') {
            throw new RuntimeException('Only scheduled rides can be booked.');
        }

        if ($ride->departure_time->lessThanOrEqualTo(now())) {
            throw new RuntimeException('Only future rides can be booked.');
        }

        if ($ride->driverProfile?->user?->account_status !== 'active') {
            throw new RuntimeException('This ride is not available for booking.');
        }

        if ($ride->available_seats < $seatsRequested) {
            throw new RuntimeException('Not enough seats available for this booking.');
        }
    }

    private function isPubliclyVisible(Ride $ride): bool
    {
        return $ride->status === 'scheduled'
            && $ride->available_seats > 0
            && $ride->departure_time->isFuture()
            && $ride->driverProfile?->user?->account_status === 'active';
    }

    private function assertTravelerHasNoActiveBooking(User $traveler, Ride $ride): void
    {
        $hasActiveBooking = Booking::query()
            ->where('ride_id', $ride->id)
            ->where('traveler_id', $traveler->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($hasActiveBooking) {
            throw new RuntimeException('You already have an active booking request for this ride.');
        }
    }

    private function assertDriverCanHandleBooking(User $driver, Booking $booking): void
    {
        if ($booking->ride?->driverProfile?->user_id !== $driver->id) {
            throw new RuntimeException('This booking does not belong to one of your rides.');
        }

        if ($booking->status !== 'pending') {
            throw new RuntimeException('Only pending booking requests can be handled.');
        }

        if ($booking->ride?->status !== 'scheduled') {
            throw new RuntimeException('Only scheduled rides can receive booking decisions.');
        }

        if ($booking->ride?->departure_time?->lessThanOrEqualTo(now())) {
            throw new RuntimeException('Past rides cannot receive booking decisions.');
        }
    }

    private function assertDriverCanCompleteRide(User $driver, Ride $ride): void
    {
        if ($ride->driverProfile?->user_id !== $driver->id) {
            throw new RuntimeException('This ride does not belong to you.');
        }

        if ($ride->status !== 'scheduled') {
            throw new RuntimeException('Only scheduled rides can be completed.');
        }

        if ($ride->departure_time->isFuture()) {
            throw new RuntimeException('Future rides cannot be completed yet.');
        }
    }

    private function assertDriverCanEditRide(User $driver, Ride $ride): void
    {
        if ($ride->driverProfile?->user_id !== $driver->id) {
            throw new RuntimeException('This ride does not belong to you.');
        }

        if ($ride->status !== 'scheduled') {
            throw new RuntimeException('Only scheduled rides can be edited.');
        }

        if ($ride->departure_time->lessThanOrEqualTo(now())) {
            throw new RuntimeException('Past rides cannot be edited.');
        }
    }

    private function assertDriverCanCancelRide(User $driver, Ride $ride): void
    {
        if ($ride->driverProfile?->user_id !== $driver->id) {
            throw new RuntimeException('This ride does not belong to you.');
        }

        if ($ride->status !== 'scheduled') {
            throw new RuntimeException('Only scheduled rides can be cancelled.');
        }

        if ($ride->departure_time->lessThanOrEqualTo(now())) {
            throw new RuntimeException('Past rides cannot be cancelled.');
        }
    }
}

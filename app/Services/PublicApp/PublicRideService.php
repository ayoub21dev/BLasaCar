<?php

namespace App\Services\PublicApp;

use App\Models\Booking;
use App\Models\Ride;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class PublicRideService
{
    /**
     * @return Collection<int, Ride>
     */
    public function searchRides(int $departureCityId, int $arrivalCityId, ?Carbon $departureDate = null): Collection
    {
        $query = Ride::query()
            ->with([
                'user.driverProfile',
                'vehicle',
                'departureCity',
                'arrivalCity',
            ])
            ->where('departure_city_id', $departureCityId)
            ->where('arrival_city_id', $arrivalCityId)
            ->where('status', 'scheduled')
            ->where('available_seats', '>', 0)
            ->where('departure_time', '>', now())
            ->whereHas('user', fn ($builder) => $builder->where('account_status', 'active'))
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
            'user.driverProfile',
            'vehicle',
            'departureCity',
            'arrivalCity',
            'bookings.traveler',
        ]);
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

            $booking = Booking::query()->create([
                'ride_id' => $lockedRide->id,
                'traveler_id' => $traveler->id,
                'seats_reserved' => $seatsRequested,
                'status' => 'pending',
                'booked_at' => now(),
            ]);

            $lockedRide->decrement('available_seats', $seatsRequested);

            return $booking->fresh(['ride', 'traveler']);
        });
    }

    /**
     * @return Collection<int, Booking>
     */
    public function listBookingStatuses(User $traveler): Collection
    {
        return Booking::query()
            ->with([
                'ride.user',
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

            return $lockedBooking->fresh(['ride', 'traveler']);
        });
    }

    public function confirmBooking(User $driver, Booking $booking): Booking
    {
        return DB::transaction(function () use ($driver, $booking): Booking {
            /** @var Booking $lockedBooking */
            $lockedBooking = Booking::query()
                ->with('ride')
                ->whereKey($booking->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $this->assertDriverCanHandleBooking($driver, $lockedBooking);

            $lockedBooking->update([
                'status' => 'confirmed',
            ]);

            return $lockedBooking->fresh(['ride', 'traveler']);
        });
    }

    public function rejectBooking(User $driver, Booking $booking): Booking
    {
        return DB::transaction(function () use ($driver, $booking): Booking {
            /** @var Booking $lockedBooking */
            $lockedBooking = Booking::query()
                ->with('ride')
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

            return $lockedBooking->fresh(['ride', 'traveler']);
        });
    }

    private function assertRideCanBeBooked(User $traveler, Ride $ride, int $seatsRequested): void
    {
        if ($traveler->account_status !== 'active') {
            throw new RuntimeException('Suspended users cannot book rides.');
        }

        if ($ride->user_id === $traveler->id) {
            throw new RuntimeException('Drivers cannot book their own rides.');
        }

        if ($ride->status !== 'scheduled') {
            throw new RuntimeException('Only scheduled rides can be booked.');
        }

        if ($ride->departure_time->lessThanOrEqualTo(now())) {
            throw new RuntimeException('Only future rides can be booked.');
        }

        if ($ride->user?->account_status !== 'active') {
            throw new RuntimeException('This ride is not available for booking.');
        }

        if ($ride->available_seats < $seatsRequested) {
            throw new RuntimeException('Not enough seats available for this booking.');
        }
    }

    private function assertDriverCanHandleBooking(User $driver, Booking $booking): void
    {
        if ($booking->ride?->user_id !== $driver->id) {
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
}

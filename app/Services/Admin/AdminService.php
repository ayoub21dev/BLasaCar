<?php

namespace App\Services\Admin;

use App\Models\Booking;
use App\Models\Ride;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

class AdminService
{
    private const USER_STATUSES = ['active', 'suspended'];

    private const RIDE_STATUSES = ['scheduled', 'completed', 'cancelled'];

    /**
     * @return array<string, int>
     */
    public function dashboardMetrics(): array
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('account_status', 'active')->count(),
            'suspended_users' => User::where('account_status', 'suspended')->count(),
            'verified_drivers' => User::whereHas('driverProfile', fn ($query) => $query->where('cin_verified', true))->count(),
            'scheduled_rides' => Ride::where('status', 'scheduled')->count(),
            'completed_rides' => Ride::where('status', 'completed')->count(),
            'cancelled_rides' => Ride::where('status', 'cancelled')->count(),
            'total_bookings' => Booking::count(),
        ];
    }

    /**
     * @return Collection<int, User>
     */
    public function listUsers(?string $status = null): Collection
    {
        $query = User::query()
            ->with('driverProfile')
            ->orderBy('first_name')
            ->orderBy('last_name');

        if ($status !== null) {
            $this->assertValidUserStatus($status);
            $query->where('account_status', $status);
        }

        return $query->get();
    }

    public function suspendUser(User $user, ?Carbon $suspendedAt = null): User
    {
        $user->forceFill([
            'account_status' => 'suspended',
            'suspended_at' => $suspendedAt ?? now(),
        ])->save();

        return $user->refresh();
    }

    public function activateUser(User $user): User
    {
        $user->forceFill([
            'account_status' => 'active',
            'suspended_at' => null,
        ])->save();

        return $user->refresh();
    }

    /**
     * @return Collection<int, Ride>
     */
    public function listRides(?string $status = null): Collection
    {
        $query = Ride::query()
            ->with(['user', 'vehicle', 'departureCity', 'arrivalCity'])
            ->orderByDesc('departure_time');

        if ($status !== null) {
            $this->assertValidRideStatus($status);
            $query->where('status', $status);
        }

        return $query->get();
    }

    public function moderateRide(Ride $ride, string $status, ?string $adminNote = null): Ride
    {
        $this->assertValidRideStatus($status);

        $ride->forceFill([
            'status' => $status,
            'admin_note' => $adminNote,
        ])->save();

        return $ride->refresh();
    }

    private function assertValidUserStatus(string $status): void
    {
        if (! in_array($status, self::USER_STATUSES, true)) {
            throw new InvalidArgumentException("Unsupported user status [{$status}].");
        }
    }

    private function assertValidRideStatus(string $status): void
    {
        if (! in_array($status, self::RIDE_STATUSES, true)) {
            throw new InvalidArgumentException("Unsupported ride status [{$status}].");
        }
    }
}

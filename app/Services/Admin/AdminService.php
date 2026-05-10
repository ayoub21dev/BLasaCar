<?php

namespace App\Services\Admin;

use App\Models\Booking;
use App\Models\DriverProfile;
use App\Models\Ride;
use App\Models\User;
use App\Support\DriverIdentityPhotos;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AdminService
{
    private const USER_STATUSES = ['active', 'suspended'];

    private const RIDE_STATUSES = ['completed', 'cancelled'];

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
            'pending_driver_verifications' => DriverProfile::where('cin_verified', false)->count(),
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
            ->with('driverProfile.vehicles')
            ->orderBy('first_name')
            ->orderBy('last_name');

        if ($status !== null) {
            $this->assertValidUserStatus($status);
            $query->where('account_status', $status);
        }

        return $query->get();
    }

    /**
     * @return Collection<int, DriverProfile>
     */
    public function listPendingDriverVerifications(): Collection
    {
        return DriverProfile::query()
            ->with(['user', 'vehicles'])
            ->where('cin_verified', false)
            ->orderBy('created_at')
            ->get();
    }

    public function suspendUser(User $user, ?Carbon $suspendedAt = null): User
    {
        $user->forceFill([
            'account_status' => 'suspended',
            'suspended_at' => $suspendedAt ?? now(),
        ])->save();

        DB::table('sessions')->where('user_id', $user->id)->delete();

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
            ->with(['driverProfile.user', 'vehicle', 'departureCity', 'arrivalCity'])
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

        DB::transaction(function () use ($ride, $status, $adminNote): void {
            /** @var Ride $lockedRide */
            $lockedRide = Ride::query()
                ->whereKey($ride->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $attributes = [
                'status' => $status,
                'admin_note' => $adminNote,
            ];

            if (in_array($status, ['cancelled', 'completed'], true)) {
                $attributes['available_seats'] = 0;
            }

            $lockedRide->forceFill($attributes)->save();

            if ($status === 'cancelled') {
                $lockedRide->bookings()
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->update(['status' => 'cancelled']);
            }

            if ($status === 'completed') {
                $lockedRide->bookings()
                    ->where('status', 'pending')
                    ->update(['status' => 'rejected']);

                $lockedRide->bookings()
                    ->where('status', 'confirmed')
                    ->update(['status' => 'completed']);
            }
        });

        return $ride->refresh();
    }

    public function verifyDriverProfile(DriverProfile $driverProfile): DriverProfile
    {
        if (
            ! DriverIdentityPhotos::exists($driverProfile, DriverIdentityPhotos::FRONT)
            || ! DriverIdentityPhotos::exists($driverProfile, DriverIdentityPhotos::BACK)
        ) {
            throw new InvalidArgumentException('Driver profile cannot be verified without both front and back CIN photos.');
        }

        $driverProfile->forceFill([
            'cin_verified' => true,
        ])->save();

        return $driverProfile->refresh();
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

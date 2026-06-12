<?php

namespace App\Http\Controllers;

use App\Models\DriverProfile;
use App\Models\User;
use App\Services\Admin\AdminService;
use App\Support\DriverIdentityPhotos;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminWorkflowController extends Controller
{
    /**
     * Verify a driver's CIN photos from the admin dashboard.
     */
    public function verifyDriverProfile(DriverProfile $driverProfile, AdminService $adminService): RedirectResponse
    {
        try {
            $adminService->verifyDriverProfile($driverProfile);
        } catch (InvalidArgumentException $exception) {
            return back()->withErrors(['driver_profile' => $exception->getMessage()]);
        }

        return back()->with('status', 'Driver profile verified.');
    }

    /**
     * Suspend a user while preventing admins from suspending themselves.
     */
    public function suspendUser(User $user, AdminService $adminService): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'You cannot suspend your own account.']);
        }

        $adminService->suspendUser($user);

        return back()->with('status', 'User suspended.');
    }

    /**
     * Reactivate a suspended user from the admin dashboard.
     */
    public function activateUser(User $user, AdminService $adminService): RedirectResponse
    {
        $adminService->activateUser($user);

        return back()->with('status', 'User activated.');
    }

    /**
     * Stream a driver's stored CIN photo to authorized admins.
     */
    public function showDriverProfileCinPhoto(DriverProfile $driverProfile, string $side): StreamedResponse
    {
        $path = DriverIdentityPhotos::path($driverProfile, $side);

        abort_if(blank($path) || ! DriverIdentityPhotos::exists($driverProfile, $side), 404);

        return Storage::disk(DriverIdentityPhotos::DISK)->response($path);
    }
}

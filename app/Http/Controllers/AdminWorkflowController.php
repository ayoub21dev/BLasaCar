<?php

namespace App\Http\Controllers;

use App\Models\DriverProfile;
use App\Services\Admin\AdminService;
use App\Support\DriverIdentityPhotos;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminWorkflowController extends Controller
{
    public function verifyDriverProfile(DriverProfile $driverProfile, AdminService $adminService): RedirectResponse
    {
        try {
            $adminService->verifyDriverProfile($driverProfile);
        } catch (InvalidArgumentException $exception) {
            return back()->withErrors(['driver_profile' => $exception->getMessage()]);
        }

        return back()->with('status', 'Driver profile verified.');
    }

    public function showDriverProfileCinPhoto(DriverProfile $driverProfile, string $side): StreamedResponse
    {
        $path = DriverIdentityPhotos::path($driverProfile, $side);

        abort_if(blank($path) || ! DriverIdentityPhotos::exists($driverProfile, $side), 404);

        return Storage::disk(DriverIdentityPhotos::DISK)->response($path);
    }
}

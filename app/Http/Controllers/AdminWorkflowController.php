<?php

namespace App\Http\Controllers;

use App\Models\DriverProfile;
use App\Services\Admin\AdminService;
use Illuminate\Http\RedirectResponse;
use InvalidArgumentException;

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
}

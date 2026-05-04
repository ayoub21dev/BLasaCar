<?php

namespace App\Http\Controllers;

use App\Models\DriverProfile;
use App\Services\Admin\AdminService;
use Illuminate\Http\RedirectResponse;

class AdminWorkflowController extends Controller
{
    public function verifyDriverProfile(DriverProfile $driverProfile, AdminService $adminService): RedirectResponse
    {
        $adminService->verifyDriverProfile($driverProfile);

        return back()->with('status', 'Driver profile verified.');
    }
}

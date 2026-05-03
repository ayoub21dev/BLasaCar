<?php

namespace App\Http\Controllers;

use App\Http\Requests\BecomeDriverRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DriverOnboardingController extends Controller
{
    public function create(): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user?->driverProfile !== null || $user?->isDriver()) {
            return redirect()->route('dashboards.driver');
        }

        return view('pages.drivers.onboarding');
    }

    public function store(BecomeDriverRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated): void {
            /** @var User $user */
            $user = User::query()
                ->whereKey($request->user()->id)
                ->lockForUpdate()
                ->firstOrFail();

            $driverProfile = $user->driverProfile()->create([
                'cin_number' => $validated['cin_number'],
                'cin_photo' => null,
                'cin_verified' => false,
            ]);

            $driverProfile->vehicles()->create([
                'brand' => $validated['vehicle_brand'],
                'model' => $validated['vehicle_model'],
                'photo' => null,
            ]);

            $user->forceFill([
                'role' => User::ROLE_DRIVER,
            ])->save();
        });

        return redirect()->route('dashboards.driver')
            ->with('status', 'Your driver account is ready. You can publish your first ride now.');
    }
}

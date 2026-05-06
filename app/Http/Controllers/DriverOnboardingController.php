<?php

namespace App\Http\Controllers;

use App\Http\Requests\BecomeDriverRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DriverOnboardingController extends Controller
{
    public function create(): Response|RedirectResponse
    {
        $user = auth()->user();

        if ($user?->driverProfile !== null || $user?->isDriver()) {
            return redirect()->route('dashboards.driver');
        }

        return Inertia::render('Drivers/Onboarding');
    }

    public function store(BecomeDriverRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $cinFrontPhotoPath = $request->file('cin_front_photo')->store('cin/front', 'public');
        $cinBackPhotoPath = $request->file('cin_back_photo')->store('cin/back', 'public');

        DB::transaction(function () use ($request, $validated, $cinFrontPhotoPath, $cinBackPhotoPath): void {
            /** @var User $user */
            $user = User::query()
                ->whereKey($request->user()->id)
                ->lockForUpdate()
                ->firstOrFail();

            $driverProfile = $user->driverProfile()->create([
                'cin_number' => $validated['cin_number'],
                'cin_photo' => $cinFrontPhotoPath,
                'cin_front_photo' => $cinFrontPhotoPath,
                'cin_back_photo' => $cinBackPhotoPath,
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

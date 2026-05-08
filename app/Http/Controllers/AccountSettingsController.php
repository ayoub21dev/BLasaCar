<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAccountPasswordRequest;
use App\Http\Requests\UpdateAccountProfileRequest;
use App\Support\InertiaProps;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class AccountSettingsController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Account/Settings', [
            'user' => InertiaProps::user(auth()->user()->load('driverProfile.vehicles'), true),
        ]);
    }

    public function updateProfile(UpdateAccountProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        if ($validated['email'] !== $user->email) {
            $validated['email_verified'] = false;
        }

        if ($validated['phone'] !== $user->phone) {
            $validated['phone_verified'] = false;
        }

        $user->update($validated);

        return back()->with('status', 'Your account details have been updated.');
    }

    public function updatePassword(UpdateAccountPasswordRequest $request): RedirectResponse
    {
        $user = $request->user();

        if (! Hash::check($request->validated('current_password'), $user->password_hash)) {
            return back()
                ->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->forceFill([
            'password_hash' => $request->validated('password'),
        ])->save();

        $request->session()->regenerate();

        return back()->with('status', 'Your password has been updated.');
    }
}

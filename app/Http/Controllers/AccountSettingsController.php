<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAccountPasswordRequest;
use App\Http\Requests\UpdateAccountProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountSettingsController extends Controller
{
    public function edit(): View
    {
        return view('pages.account.settings', [
            'user' => auth()->user()->load('driverProfile'),
        ]);
    }

    public function updateProfile(UpdateAccountProfileRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

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

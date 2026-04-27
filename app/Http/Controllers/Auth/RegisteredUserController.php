<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    public function store(RegisterRequest $request): RedirectResponse
    {
        $name = Str::of($request->validated('full_name'))->squish();
        $nameParts = explode(' ', $name->toString(), 2);

        $user = User::query()->create([
            'first_name' => $nameParts[0],
            'last_name' => $nameParts[1] ?? '',
            'email' => $request->validated('email'),
            'phone' => $request->validated('phone'),
            'password_hash' => $request->validated('password'),
            'email_verified' => false,
            'phone_verified' => false,
            'account_status' => 'active',
            'role' => User::ROLE_TRAVELER,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($user->dashboardRoute())
            ->with('status', 'Your account has been created.');
    }
}

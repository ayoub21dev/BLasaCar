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

        if ($this->expectsMobileRedirect($request)) {
            return redirect($this->mobileRedirectUrl($request, route('mobile.home')))
                ->with('status', 'Your account has been created.');
        }

        return redirect()->route($user->dashboardRoute())
            ->with('status', 'Your account has been created.');
    }

    private function expectsMobileRedirect(RegisterRequest $request): bool
    {
        $redirectTo = (string) $request->input('redirect_to', '');
        $referer = (string) $request->headers->get('referer', '');

        return str_starts_with($redirectTo, '/mobile')
            || str_contains($referer, '/mobile');
    }

    private function mobileRedirectUrl(RegisterRequest $request, string $fallback): string
    {
        $redirectTo = (string) $request->input('redirect_to', '');

        return str_starts_with($redirectTo, '/mobile') ? $redirectTo : $fallback;
    }
}

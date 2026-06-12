<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Sign in an active account and send users to the correct dashboard or mobile page.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = [
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'account_status' => 'active',
        ];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors([
                    'email' => 'The provided credentials do not match an active account.',
                ])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        if ($this->expectsMobileRedirect($request)) {
            return redirect()->intended($this->mobileRedirectUrl($request, route('mobile.home')));
        }

        return redirect()->intended(route($request->user()->dashboardRoute()));
    }

    /**
     * Sign out the current web session and redirect to the right home page.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $route = $this->expectsMobileRedirect($request) ? 'mobile.home' : 'home';

        return redirect()->route($route)->with('status', 'You have been logged out.');
    }

    /**
     * Detect whether auth was submitted from the mobile Inertia flow.
     */
    private function expectsMobileRedirect(Request $request): bool
    {
        $redirectTo = (string) $request->input('redirect_to', '');
        $referer = (string) $request->headers->get('referer', '');

        return str_starts_with($redirectTo, '/mobile')
            || str_contains($referer, '/mobile');
    }

    /**
     * Keep mobile auth redirects inside the mobile route namespace.
     */
    private function mobileRedirectUrl(Request $request, string $fallback): string
    {
        $redirectTo = (string) $request->input('redirect_to', '');

        return str_starts_with($redirectTo, '/mobile') ? $redirectTo : $fallback;
    }
}

<?php

namespace App\Http\Middleware;

use App\Models\MobileApiToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMobileApiToken
{
    /**
     * Authenticate a mobile API request from its bearer token.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $plainTextToken = $request->bearerToken();

        if ($plainTextToken === null || $plainTextToken === '') {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $token = MobileApiToken::query()
            ->with('user.driverProfile.vehicles')
            ->where('token_hash', hash('sha256', $plainTextToken))
            ->first();

        if ($token === null || $token->isExpired()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user = $token->user;

        if ($user === null || $user->account_status !== 'active') {
            return response()->json(['message' => 'Your account is suspended.'], 403);
        }

        $token->forceFill(['last_used_at' => now()])->save();

        Auth::setUser($user);
        $request->setUserResolver(fn () => $user);
        $request->attributes->set('mobileApiToken', $token);

        return $next($request);
    }
}

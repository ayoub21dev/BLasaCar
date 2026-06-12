<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMobileApiRole
{
    /**
     * Allow only mobile users with one of the required roles to continue.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (in_array($user->role, $roles, true)) {
            return $next($request);
        }

        return response()->json(['message' => 'This action is not available for your account.'], 403);
    }
}

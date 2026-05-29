<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Mobile\LoginRequest;
use App\Http\Requests\Api\Mobile\RegisterRequest;
use App\Models\MobileApiToken;
use App\Models\User;
use App\Support\InertiaProps;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
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

        return $this->tokenResponse($user, $request->validated('device_name', 'mobile'), 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = [
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'account_status' => 'active',
        ];

        if (! Auth::validate($credentials)) {
            return response()->json([
                'message' => 'The provided credentials do not match an active account.',
                'errors' => [
                    'email' => ['The provided credentials do not match an active account.'],
                ],
            ], 422);
        }

        $user = User::query()
            ->with('driverProfile.vehicles')
            ->where('email', $request->validated('email'))
            ->firstOrFail();

        return $this->tokenResponse($user, $request->validated('device_name', 'mobile'));
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => InertiaProps::user($request->user()->loadMissing('driverProfile.vehicles'), true),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->attributes->get('mobileApiToken')?->delete();

        return response()->json(null, 204);
    }

    private function tokenResponse(User $user, string $deviceName, int $status = 200): JsonResponse
    {
        [$token, $plainTextToken] = MobileApiToken::issueFor($user, $deviceName);

        return response()->json([
            'data' => [
                'token' => $plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => $token->expires_at?->toIso8601String(),
                'user' => InertiaProps::user($user->loadMissing('driverProfile.vehicles'), true),
            ],
        ], $status);
    }
}

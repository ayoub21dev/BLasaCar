<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Mobile\BookRideRequest;
use App\Models\Booking;
use App\Models\Ride;
use App\Services\PublicApp\PublicRideService;
use App\Support\InertiaProps;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use RuntimeException;

class BookingController extends Controller
{
    /**
     * Return the authenticated traveler's mobile booking list.
     */
    public function index(Request $request, PublicRideService $publicRideService): JsonResponse
    {
        $bookings = $publicRideService->listBookingStatuses($request->user())
            ->map(fn (Booking $booking) => InertiaProps::booking($booking))
            ->values();

        return response()->json([
            'data' => $bookings,
            'meta' => [
                'count' => $bookings->count(),
            ],
        ]);
    }

    /**
     * Create a pending booking request for the authenticated traveler.
     */
    public function store(BookRideRequest $request, Ride $ride, PublicRideService $publicRideService): JsonResponse
    {
        try {
            $booking = $publicRideService->requestSeat($request->user(), $ride, (int) $request->validated('seats'));
        } catch (InvalidArgumentException|RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'errors' => [
                    'seats' => [$exception->getMessage()],
                ],
            ], 422);
        }

        return response()->json([
            'data' => InertiaProps::booking($booking),
        ], 201);
    }

    /**
     * Cancel the authenticated traveler's own pending or confirmed booking.
     */
    public function cancel(Request $request, Booking $booking, PublicRideService $publicRideService): JsonResponse
    {
        try {
            $booking = $publicRideService->cancelTravelerBooking($request->user(), $booking);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'errors' => [
                    'booking' => [$exception->getMessage()],
                ],
            ], 422);
        }

        return response()->json([
            'data' => InertiaProps::booking($booking),
        ]);
    }
}

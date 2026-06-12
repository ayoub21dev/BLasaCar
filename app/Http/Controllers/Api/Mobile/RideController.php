<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Mobile\RideIndexRequest;
use App\Models\Ride;
use App\Services\PublicApp\PublicRideService;
use App\Support\InertiaProps;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class RideController extends Controller
{
    /**
     * Return bookable mobile rides, optionally filtered by route, date, seats, and limit.
     */
    public function index(RideIndexRequest $request, PublicRideService $publicRideService): JsonResponse
    {
        $filters = $request->validated();
        $limit = (int) ($filters['limit'] ?? 12);

        if (! empty($filters['departure_city_id']) && ! empty($filters['arrival_city_id'])) {
            $rides = $publicRideService->searchRides(
                (int) $filters['departure_city_id'],
                (int) $filters['arrival_city_id'],
                ! empty($filters['departure_date']) ? Carbon::parse($filters['departure_date']) : null,
            )->take($limit);
        } else {
            $rides = $publicRideService->listBookableRides($limit);
        }

        if (! empty($filters['seats'])) {
            $rides = $rides
                ->filter(fn (Ride $ride) => $ride->available_seats >= (int) $filters['seats'])
                ->values();
        }

        $payload = $rides
            ->map(fn (Ride $ride) => InertiaProps::ride($ride))
            ->values();

        return response()->json([
            'data' => $payload,
            'meta' => [
                'count' => $payload->count(),
            ],
        ]);
    }

    /**
     * Return public ride details for a mobile client.
     */
    public function show(Ride $ride, PublicRideService $publicRideService): JsonResponse
    {
        abort_unless($publicRideService->canViewRideDetails(null, $ride), 404);

        return response()->json([
            'data' => InertiaProps::ride($publicRideService->getRideDetails($ride)),
        ]);
    }
}

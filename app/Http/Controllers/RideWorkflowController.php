<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRideRequest;
use App\Http\Requests\PublishRideRequest;
use App\Models\Ride;
use App\Services\PublicApp\PublicRideService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use RuntimeException;

class RideWorkflowController extends Controller
{
    public function store(PublishRideRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $ride = Ride::query()->create([
            'user_id' => $request->user()->id,
            'vehicle_id' => $validated['vehicle_id'],
            'departure_city_id' => $validated['departure_city_id'],
            'arrival_city_id' => $validated['arrival_city_id'],
            'departure_time' => Carbon::parse($validated['departure_date'].' '.$validated['departure_time']),
            'price_per_seat' => $validated['price_per_seat'],
            'total_seats' => $validated['seats_offered'],
            'available_seats' => $validated['seats_offered'],
            'meeting_point' => $validated['meeting_point'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'scheduled',
        ]);

        return redirect()->route('rides.show', $ride)
            ->with('status', 'Your ride has been published.');
    }

    public function book(BookRideRequest $request, Ride $ride, PublicRideService $publicRideService): RedirectResponse
    {
        try {
            $publicRideService->requestSeat($request->user(), $ride, (int) $request->validated('seats'));
        } catch (InvalidArgumentException|RuntimeException $exception) {
            return back()
                ->withErrors(['seats' => $exception->getMessage()])
                ->withInput();
        }

        return redirect()->route('dashboards.traveler')
            ->with('status', 'Your seat request has been sent.');
    }
}

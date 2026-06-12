<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRideRequest;
use App\Http\Requests\PublishRideRequest;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateRideRequest;
use App\Models\Booking;
use App\Models\Ride;
use App\Services\PublicApp\PublicRideService;
use App\Services\PublicApp\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use RuntimeException;

class RideWorkflowController extends Controller
{
    /**
     * Keep ride and review business rules in services, leaving this controller to handle HTTP responses.
     */
    public function __construct(
        private readonly PublicRideService $rides,
        private readonly ReviewService $reviews,
    ) {}

    /**
     * Publish a new scheduled ride for a verified driver.
     */
    public function store(PublishRideRequest $request): RedirectResponse
    {
        $ride = $this->rides->publishRide($request->user(), $request->validated());

        return redirect()->route('rides.show', $ride)
            ->with('status', 'Your ride has been published.');
    }

    /**
     * Save edits to a driver's future ride and report validation errors back to the form.
     */
    public function update(UpdateRideRequest $request, Ride $ride): RedirectResponse
    {
        try {
            $this->rides->updateRide($request->user(), $ride, $request->validated());
        } catch (RuntimeException $exception) {
            return back()
                ->withErrors(['ride' => $exception->getMessage()])
                ->withInput();
        }

        return redirect()->route('dashboards.driver')
            ->with('status', 'Ride updated.');
    }

    /**
     * Cancel a driver's future ride and return the user to the current page.
     */
    public function cancelRide(Request $request, Ride $ride): RedirectResponse
    {
        try {
            $this->rides->cancelRide($request->user(), $ride);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['ride' => $exception->getMessage()]);
        }

        return back()->with('status', 'Ride cancelled.');
    }

    /**
     * Turn a traveler's seat request into a pending booking.
     */
    public function book(BookRideRequest $request, Ride $ride): RedirectResponse
    {
        try {
            $this->rides->requestSeat($request->user(), $ride, (int) $request->validated('seats'));
        } catch (InvalidArgumentException|RuntimeException $exception) {
            return back()
                ->withErrors(['seats' => $exception->getMessage()])
                ->withInput();
        }

        if ($this->expectsMobileRedirect($request)) {
            return redirect($this->mobileRedirectUrl($request, route('mobile.trips')))
                ->with('status', 'Your seat request has been sent.');
        }

        return redirect()->route('dashboards.traveler')
            ->with('status', 'Your seat request has been sent.');
    }

    /**
     * Let a driver accept a pending booking request for their ride.
     */
    public function confirmBooking(Request $request, Booking $booking): RedirectResponse
    {
        try {
            $this->rides->confirmBooking($request->user(), $booking);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['booking' => $exception->getMessage()]);
        }

        return back()->with('status', 'Booking request accepted.');
    }

    /**
     * Let a driver reject a pending booking request for their ride.
     */
    public function rejectBooking(Request $request, Booking $booking): RedirectResponse
    {
        try {
            $this->rides->rejectBooking($request->user(), $booking);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['booking' => $exception->getMessage()]);
        }

        return back()->with('status', 'Booking request rejected.');
    }

    /**
     * Let a driver close a ride after departure.
     */
    public function completeRide(Request $request, Ride $ride): RedirectResponse
    {
        try {
            $this->rides->completeRide($request->user(), $ride);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['ride' => $exception->getMessage()]);
        }

        return back()->with('status', 'Ride completed.');
    }

    /**
     * Let a traveler cancel only their own active booking.
     */
    public function cancelBooking(Request $request, Booking $booking): RedirectResponse
    {
        try {
            $this->rides->cancelTravelerBooking($request->user(), $booking);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['booking' => $exception->getMessage()]);
        }

        return back()->with('status', 'Booking cancelled.');
    }

    /**
     * Save a traveler review for their completed booking.
     */
    public function reviewBooking(StoreReviewRequest $request, Booking $booking): RedirectResponse
    {
        try {
            $this->reviews->submitDriverReview(
                traveler: $request->user(),
                booking: $booking,
                rating: (int) $request->validated('rating'),
                comment: $request->validated('comment') ?? null,
            );
        } catch (RuntimeException $exception) {
            return back()
                ->withErrors(['review' => $exception->getMessage()])
                ->withInput();
        }

        return back()->with('status', 'Review submitted.');
    }

    /**
     * Detect whether a shared booking endpoint was submitted from the mobile UI.
     */
    private function expectsMobileRedirect($request): bool
    {
        $redirectTo = (string) $request->input('redirect_to', '');
        $referer = (string) $request->headers->get('referer', '');

        return str_starts_with($redirectTo, '/mobile')
            || str_contains($referer, '/mobile');
    }

    /**
     * Prefer a safe mobile redirect target when the booking came from mobile pages.
     */
    private function mobileRedirectUrl($request, string $fallback): string
    {
        $redirectTo = (string) $request->input('redirect_to', '');

        return str_starts_with($redirectTo, '/mobile') ? $redirectTo : $fallback;
    }
}

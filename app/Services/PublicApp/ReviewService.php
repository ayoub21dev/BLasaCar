<?php

namespace App\Services\PublicApp;

use App\Models\Booking;
use App\Models\DriverProfile;
use App\Models\Review;
use App\Models\User;
use App\Services\Notifications\ReviewNotificationService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ReviewService
{
    public function __construct(
        private readonly ReviewNotificationService $notifications,
    ) {}

    public function submitDriverReview(User $traveler, Booking $booking, int $rating, ?string $comment = null): Review
    {
        return DB::transaction(function () use ($traveler, $booking, $rating, $comment): Review {
            /** @var Booking $lockedBooking */
            $lockedBooking = Booking::query()
                ->with([
                    'ride.driverProfile.user',
                    'ride.departureCity',
                    'ride.arrivalCity',
                    'traveler',
                ])
                ->whereKey($booking->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $this->assertTravelerCanReviewDriver($traveler, $lockedBooking);

            try {
                $review = Review::query()->create([
                    'booking_id' => $lockedBooking->id,
                    'traveler_id' => $traveler->id,
                    'driver_profile_id' => $lockedBooking->ride->driver_profile_id,
                    'rating' => $rating,
                    'comment' => $comment,
                ]);
            } catch (QueryException $exception) {
                throw new RuntimeException('You already reviewed this ride.', previous: $exception);
            }

            $this->refreshDriverRating($lockedBooking->ride->driverProfile);

            $review = $review->fresh(['traveler', 'driverProfile.user', 'booking.ride.departureCity', 'booking.ride.arrivalCity']);
            $this->notifications->reviewReceived($review);

            return $review;
        });
    }

    private function assertTravelerCanReviewDriver(User $traveler, Booking $booking): void
    {
        if ($booking->traveler_id !== $traveler->id) {
            throw new RuntimeException('You can only review your own bookings.');
        }

        if ($booking->status !== 'completed') {
            throw new RuntimeException('Only completed trips can be reviewed.');
        }

        if ($booking->ride?->driverProfile?->user_id === $traveler->id) {
            throw new RuntimeException('You cannot review yourself.');
        }

        if ($booking->ride?->driverProfile === null) {
            throw new RuntimeException('This driver profile is not available for review.');
        }

        $alreadyReviewed = Review::query()
            ->where('booking_id', $booking->id)
            ->exists();

        if ($alreadyReviewed) {
            throw new RuntimeException('You already reviewed this ride.');
        }
    }

    private function refreshDriverRating(DriverProfile $driverProfile): void
    {
        $averageRating = Review::query()
            ->where('driver_profile_id', $driverProfile->id)
            ->avg('rating');

        $driverProfile->update([
            'avg_rating' => round((float) $averageRating, 2),
        ]);
    }
}

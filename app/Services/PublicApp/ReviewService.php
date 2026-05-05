<?php

namespace App\Services\PublicApp;

use App\Models\Booking;
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
                    'ride.user.driverProfile',
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
                    'reviewer_id' => $traveler->id,
                    'reviewed_user_id' => $lockedBooking->ride->user_id,
                    'ride_id' => $lockedBooking->ride_id,
                    'rating' => $rating,
                    'comment' => $comment,
                ]);
            } catch (QueryException $exception) {
                throw new RuntimeException('You already reviewed this ride.', previous: $exception);
            }

            $this->refreshDriverRating($lockedBooking->ride->user);

            $review = $review->fresh(['reviewer', 'reviewedUser', 'ride.departureCity', 'ride.arrivalCity']);
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

        if ($booking->ride?->user_id === $traveler->id) {
            throw new RuntimeException('You cannot review yourself.');
        }

        if ($booking->ride?->user?->driverProfile === null) {
            throw new RuntimeException('This driver profile is not available for review.');
        }

        $alreadyReviewed = Review::query()
            ->where('reviewer_id', $traveler->id)
            ->where('reviewed_user_id', $booking->ride->user_id)
            ->where('ride_id', $booking->ride_id)
            ->exists();

        if ($alreadyReviewed) {
            throw new RuntimeException('You already reviewed this ride.');
        }
    }

    private function refreshDriverRating(User $driver): void
    {
        $averageRating = Review::query()
            ->where('reviewed_user_id', $driver->id)
            ->avg('rating');

        $driver->driverProfile?->update([
            'avg_rating' => round((float) $averageRating, 2),
        ]);
    }
}

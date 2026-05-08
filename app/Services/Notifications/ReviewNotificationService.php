<?php

namespace App\Services\Notifications;

use App\Models\Notification;
use App\Models\Review;
use App\Models\User;

class ReviewNotificationService
{
    public function reviewReceived(Review $review): Notification
    {
        $review = $review->loadMissing([
            'traveler',
            'driverProfile.user',
            'booking.ride.departureCity',
            'booking.ride.arrivalCity',
        ]);

        return Notification::query()->create([
            'user_id' => $review->driverProfile->user_id,
            'type' => 'review_received',
            'channel' => 'in_app',
            'title' => 'New review',
            'message' => sprintf(
                '%s rated you %d/5 for the %s ride.',
                $this->userName($review->traveler),
                $review->rating,
                $this->routeLabel($review),
            ),
            'ride_id' => $review->booking->ride_id,
            'booking_id' => $review->booking_id,
            'is_read' => false,
        ]);
    }

    private function routeLabel(Review $review): string
    {
        return sprintf(
            '%s to %s',
            $review->booking?->ride?->departureCity?->name ?? 'departure',
            $review->booking?->ride?->arrivalCity?->name ?? 'arrival',
        );
    }

    private function userName(User $user): string
    {
        return trim($user->first_name.' '.$user->last_name) ?: $user->email;
    }
}

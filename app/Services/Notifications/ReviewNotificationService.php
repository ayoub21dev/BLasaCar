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
            'reviewer',
            'reviewedUser',
            'ride.departureCity',
            'ride.arrivalCity',
        ]);

        return Notification::query()->create([
            'user_id' => $review->reviewed_user_id,
            'type' => 'review_received',
            'channel' => 'in_app',
            'title' => 'New review',
            'message' => sprintf(
                '%s rated you %d/5 for the %s ride.',
                $this->userName($review->reviewer),
                $review->rating,
                $this->routeLabel($review),
            ),
            'related_entity_type' => Review::class,
            'related_entity_id' => $review->id,
            'is_read' => false,
        ]);
    }

    private function routeLabel(Review $review): string
    {
        return sprintf(
            '%s to %s',
            $review->ride?->departureCity?->name ?? 'departure',
            $review->ride?->arrivalCity?->name ?? 'arrival',
        );
    }

    private function userName(User $user): string
    {
        return trim($user->first_name.' '.$user->last_name) ?: $user->email;
    }
}

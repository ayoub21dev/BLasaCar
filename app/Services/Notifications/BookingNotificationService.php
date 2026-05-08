<?php

namespace App\Services\Notifications;

use App\Models\Booking;
use App\Models\Notification;
use App\Models\User;

class BookingNotificationService
{
    public function bookingRequested(Booking $booking): Notification
    {
        $booking = $this->loadBookingContext($booking);

        return $this->createForUser(
            user: $booking->ride->driverProfile->user,
            type: 'new_booking',
            title: 'New booking request',
            message: sprintf(
                '%s requested %s on your %s ride.',
                $this->userName($booking->traveler),
                $this->seatLabel($booking),
                $this->routeLabel($booking),
            ),
            booking: $booking,
        );
    }

    public function bookingConfirmed(Booking $booking): Notification
    {
        $booking = $this->loadBookingContext($booking);

        return $this->createForUser(
            user: $booking->traveler,
            type: 'booking_accepted',
            title: 'Booking accepted',
            message: sprintf(
                'Your request for %s on the %s ride was accepted.',
                $this->seatLabel($booking),
                $this->routeLabel($booking),
            ),
            booking: $booking,
        );
    }

    public function bookingRejected(Booking $booking): Notification
    {
        $booking = $this->loadBookingContext($booking);

        return $this->createForUser(
            user: $booking->traveler,
            type: 'booking_rejected',
            title: 'Booking rejected',
            message: sprintf(
                'Your request for %s on the %s ride was rejected.',
                $this->seatLabel($booking),
                $this->routeLabel($booking),
            ),
            booking: $booking,
        );
    }

    public function bookingCancelledByTraveler(Booking $booking): Notification
    {
        $booking = $this->loadBookingContext($booking);

        return $this->createForUser(
            user: $booking->ride->driverProfile->user,
            type: 'booking_cancelled',
            title: 'Booking cancelled',
            message: sprintf(
                '%s cancelled %s on your %s ride.',
                $this->userName($booking->traveler),
                $this->seatLabel($booking),
                $this->routeLabel($booking),
            ),
            booking: $booking,
        );
    }

    public function rideCompleted(Booking $booking): Notification
    {
        $booking = $this->loadBookingContext($booking);

        return $this->createForUser(
            user: $booking->traveler,
            type: 'ride_completed',
            title: 'Ride completed',
            message: sprintf(
                'Your %s ride is completed. You can now review the driver.',
                $this->routeLabel($booking),
            ),
            booking: $booking,
        );
    }

    private function createForUser(User $user, string $type, string $title, string $message, Booking $booking): Notification
    {
        return Notification::query()->create([
            'user_id' => $user->id,
            'type' => $type,
            'channel' => 'in_app',
            'title' => $title,
            'message' => $message,
            'ride_id' => $booking->ride_id,
            'booking_id' => $booking->id,
            'is_read' => false,
        ]);
    }

    private function loadBookingContext(Booking $booking): Booking
    {
        return $booking->loadMissing([
            'ride.driverProfile.user',
            'ride.departureCity',
            'ride.arrivalCity',
            'traveler',
        ]);
    }

    private function routeLabel(Booking $booking): string
    {
        return sprintf(
            '%s to %s',
            $booking->ride?->departureCity?->name ?? 'departure',
            $booking->ride?->arrivalCity?->name ?? 'arrival',
        );
    }

    private function seatLabel(Booking $booking): string
    {
        return $booking->seats_reserved === 1
            ? '1 seat'
            : $booking->seats_reserved.' seats';
    }

    private function userName(User $user): string
    {
        return trim($user->first_name.' '.$user->last_name) ?: $user->email;
    }
}

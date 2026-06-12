<?php

namespace App\Services\Notifications;

use App\Jobs\SendWhatsAppNotificationJob;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\User;

class BookingNotificationService
{
    /**
     * Notify the driver when a traveler creates a pending booking request.
     */
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
            sendWhatsApp: true,
        );
    }

    /**
     * Notify the traveler when the driver accepts their booking request.
     */
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
            sendWhatsApp: true,
        );
    }

    /**
     * Notify the traveler when the driver rejects their booking request.
     */
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
            sendWhatsApp: true,
        );
    }

    /**
     * Notify the driver when a traveler cancels an active booking.
     */
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
            sendWhatsApp: true,
        );
    }

    /**
     * Notify the traveler that a completed ride is ready for review.
     */
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

    /**
     * Notify the traveler when the driver cancels a scheduled ride.
     */
    public function rideCancelledByDriver(Booking $booking): Notification
    {
        $booking = $this->loadBookingContext($booking);

        return $this->createForUser(
            user: $booking->traveler,
            type: 'ride_cancelled',
            title: 'Ride cancelled',
            message: sprintf(
                'The driver cancelled your %s ride.',
                $this->routeLabel($booking),
            ),
            booking: $booking,
            sendWhatsApp: true,
        );
    }

    /**
     * Store an in-app notification and optionally mirror it to WhatsApp.
     */
    private function createForUser(User $user, string $type, string $title, string $message, Booking $booking, bool $sendWhatsApp = false): Notification
    {
        $notification = Notification::query()->create([
            'user_id' => $user->id,
            'type' => $type,
            'channel' => 'in_app',
            'title' => $title,
            'message' => $message,
            'ride_id' => $booking->ride_id,
            'booking_id' => $booking->id,
            'is_read' => false,
        ]);

        if ($sendWhatsApp) {
            $this->createWhatsAppForUser($user, $type, $title, $message, $booking);
        }

        return $notification;
    }

    /**
     * Store a WhatsApp notification and dispatch it when a recipient phone exists.
     */
    private function createWhatsAppForUser(User $user, string $type, string $title, string $message, Booking $booking): ?Notification
    {
        if (! config('services.whatsapp.enabled')) {
            return null;
        }

        $recipientPhone = $this->e164Phone($user->phone);

        $notification = Notification::query()->create([
            'user_id' => $user->id,
            'type' => $type,
            'channel' => 'whatsapp',
            'title' => $title,
            'message' => $message,
            'recipient_phone' => $recipientPhone,
            'delivery_status' => $recipientPhone ? 'pending' : 'failed',
            'provider' => config('services.whatsapp.driver'),
            'delivery_error' => $recipientPhone ? null : 'Recipient phone is missing.',
            'ride_id' => $booking->ride_id,
            'booking_id' => $booking->id,
            'is_read' => false,
        ]);

        if ($recipientPhone) {
            SendWhatsAppNotificationJob::dispatch($notification->id)->afterCommit();
        }

        return $notification;
    }

    /**
     * Load the booking relations needed to build notification messages.
     */
    private function loadBookingContext(Booking $booking): Booking
    {
        return $booking->loadMissing([
            'ride.driverProfile.user',
            'ride.departureCity',
            'ride.arrivalCity',
            'traveler',
        ]);
    }

    /**
     * Format a booking route for notification copy.
     */
    private function routeLabel(Booking $booking): string
    {
        return sprintf(
            '%s to %s',
            $booking->ride?->departureCity?->name ?? 'departure',
            $booking->ride?->arrivalCity?->name ?? 'arrival',
        );
    }

    /**
     * Format the reserved seat count for notification copy.
     */
    private function seatLabel(Booking $booking): string
    {
        return $booking->seats_reserved === 1
            ? '1 seat'
            : $booking->seats_reserved.' seats';
    }

    /**
     * Format a user's display name for notification copy.
     */
    private function userName(User $user): string
    {
        return trim($user->first_name.' '.$user->last_name) ?: $user->email;
    }

    /**
     * Normalize phone numbers to E.164 for WhatsApp delivery.
     */
    private function e164Phone(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $trimmed = trim($phone);

        if (str_starts_with($trimmed, '+')) {
            return '+'.preg_replace('/\D+/', '', substr($trimmed, 1));
        }

        $digits = preg_replace('/\D+/', '', $trimmed);

        if (! $digits) {
            return null;
        }

        if (str_starts_with($digits, '00')) {
            return '+'.substr($digits, 2);
        }

        if (str_starts_with($digits, '0')) {
            return '+212'.substr($digits, 1);
        }

        return '+'.$digits;
    }
}

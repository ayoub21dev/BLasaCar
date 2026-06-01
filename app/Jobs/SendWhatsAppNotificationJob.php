<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Services\Notifications\WhatsApp\WhatsAppClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SendWhatsAppNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $notificationId,
    ) {}

    public function handle(WhatsAppClient $client): void
    {
        $notification = Notification::query()->find($this->notificationId);

        if (! $notification || $notification->channel !== 'whatsapp' || $notification->delivery_status === 'sent') {
            return;
        }

        if (! $notification->recipient_phone) {
            $notification->update([
                'delivery_status' => 'failed',
                'failed_at' => now(),
                'delivery_error' => 'Recipient phone is missing.',
            ]);

            return;
        }

        try {
            $result = $client->send($notification->recipient_phone, $notification->message);

            $notification->update([
                'delivery_status' => 'sent',
                'provider' => $result->provider,
                'provider_message_id' => $result->messageId,
                'sent_at' => now(),
                'failed_at' => null,
                'delivery_error' => null,
            ]);
        } catch (Throwable $exception) {
            $notification->update([
                'delivery_status' => 'failed',
                'failed_at' => now(),
                'delivery_error' => str($exception->getMessage())->limit(1000)->toString(),
            ]);
        }
    }
}

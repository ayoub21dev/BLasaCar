<?php

namespace App\Services\Notifications\WhatsApp;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LogWhatsAppClient implements WhatsAppClient
{
    public function send(string $recipientPhone, string $message): WhatsAppMessageResult
    {
        Log::info('WhatsApp notification prepared.', [
            'recipient_phone' => $recipientPhone,
            'message' => $message,
        ]);

        return new WhatsAppMessageResult('log', 'log-'.Str::uuid());
    }
}

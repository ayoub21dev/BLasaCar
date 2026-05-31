<?php

namespace App\Services\Notifications\WhatsApp;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class TwilioWhatsAppClient implements WhatsAppClient
{
    public function send(string $recipientPhone, string $message): WhatsAppMessageResult
    {
        $accountSid = (string) config('services.twilio.account_sid');
        $authToken = (string) config('services.twilio.auth_token');
        $from = (string) config('services.twilio.whatsapp_from');

        if ($accountSid === '' || $authToken === '' || $from === '') {
            throw new RuntimeException('Twilio WhatsApp credentials are not configured.');
        }

        $response = Http::asForm()
            ->withBasicAuth($accountSid, $authToken)
            ->connectTimeout(5)
            ->timeout(10)
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json", array_filter([
                'From' => $this->whatsAppAddress($from),
                'To' => $this->whatsAppAddress($recipientPhone),
                'Body' => $message,
                'StatusCallback' => config('services.twilio.status_callback_url'),
            ]));

        if ($response->failed()) {
            throw new RuntimeException($response->json('message') ?: 'Twilio WhatsApp request failed.');
        }

        return new WhatsAppMessageResult('twilio', $response->json('sid'));
    }

    private function whatsAppAddress(string $phone): string
    {
        return str_starts_with($phone, 'whatsapp:')
            ? $phone
            : 'whatsapp:'.$phone;
    }
}

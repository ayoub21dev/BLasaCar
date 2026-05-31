<?php

namespace App\Services\Notifications\WhatsApp;

interface WhatsAppClient
{
    public function send(string $recipientPhone, string $message): WhatsAppMessageResult;
}

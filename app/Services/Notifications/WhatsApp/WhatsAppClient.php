<?php

namespace App\Services\Notifications\WhatsApp;

interface WhatsAppClient
{
    /**
     * Send a WhatsApp message and return provider delivery metadata.
     */
    public function send(string $recipientPhone, string $message): WhatsAppMessageResult;
}

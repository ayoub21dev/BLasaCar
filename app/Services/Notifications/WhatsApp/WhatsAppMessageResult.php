<?php

namespace App\Services\Notifications\WhatsApp;

final readonly class WhatsAppMessageResult
{
    public function __construct(
        public string $provider,
        public ?string $messageId = null,
    ) {}
}

<?php

namespace App\Services\Notifications\WhatsApp;

final readonly class WhatsAppMessageResult
{
    /**
     * Store the provider name and optional provider message id.
     */
    public function __construct(
        public string $provider,
        public ?string $messageId = null,
    ) {}
}

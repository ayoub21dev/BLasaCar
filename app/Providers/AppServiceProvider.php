<?php

namespace App\Providers;

use App\Services\Notifications\WhatsApp\LogWhatsAppClient;
use App\Services\Notifications\WhatsApp\TwilioWhatsAppClient;
use App\Services\Notifications\WhatsApp\WhatsAppClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(WhatsAppClient::class, function (): WhatsAppClient {
            return match (config('services.whatsapp.driver')) {
                'twilio' => new TwilioWhatsAppClient,
                default => new LogWhatsAppClient,
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

<?php
namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Force APP_URL for route generation (fixes localhost:8000 issue when using proxy)
        if ($appUrl = config('app.url')) {
            \URL::forceRootUrl($appUrl);

            // Force HTTPS in production
            if (config('app.env') === 'production' || str_contains($appUrl, 'https://')) {
                \URL::forceScheme('https');
            }
        }

        Event::listen(
            \App\Events\OrderStatusChanged::class,
            \App\Listeners\SendPushOnOrderStatusChanged::class
        );
    }
}

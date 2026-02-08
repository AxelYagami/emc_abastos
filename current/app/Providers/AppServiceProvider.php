<?php
namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Event::listen(
            \App\Events\OrderStatusChanged::class,
            \App\Listeners\SendPushOnOrderStatusChanged::class
        );
    }
}

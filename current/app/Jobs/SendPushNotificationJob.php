<?php

namespace App\Jobs;

use App\Services\Push\WebPushService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 30;

    public function __construct(
        public readonly int $empresaId,
        public readonly array $roleSlugs,
        public readonly array $payload,
        public readonly ?int $orderId = null,
        public readonly ?string $eventKey = null,
    ) {}

    public function handle(WebPushService $service): void
    {
        $service->sendToRole(
            $this->empresaId,
            $this->roleSlugs,
            $this->payload,
            $this->orderId,
            $this->eventKey,
        );
    }
}

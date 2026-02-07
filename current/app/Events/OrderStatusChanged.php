<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $empresaId,
        public readonly int $orderId,
        public readonly string $tipoEntrega,
        public readonly string $fromStatus,
        public readonly string $toStatus,
        public readonly int $actorUserId,
    ) {}
}

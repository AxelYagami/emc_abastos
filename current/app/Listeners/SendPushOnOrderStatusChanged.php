<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Jobs\SendPushNotificationJob;
use App\Models\Orden;
use App\Services\OrderFlowService;

class SendPushOnOrderStatusChanged
{
    public function handle(OrderStatusChanged $event): void
    {
        $orden = Orden::find($event->orderId);
        if (!$orden) return;

        $label = OrderFlowService::statusLabel($event->toStatus);
        $tipoLabel = $event->tipoEntrega === 'delivery' ? 'Envio' : 'Pickup';

        $payload = [
            'title' => "Pedido {$orden->folio} - {$label}",
            'body'  => "{$orden->comprador_nombre} · {$tipoLabel} · \${$orden->total}",
            'url'   => "/ops/movil/orden/{$orden->id}",
            'tags'  => [
                'empresa_id' => $event->empresaId,
                'order_id'   => $event->orderId,
                'to_status'  => $event->toStatus,
            ],
        ];

        // Audiencia: roles operativos siempre
        $roles = ['operaciones', 'admin_empresa', 'superadmin'];

        // Cajero en ciertos estados
        if (in_array($event->toStatus, ['confirmado', 'entregado'])) {
            $roles[] = 'cajero';
        }

        // Repartidor solo en delivery + estados relevantes
        if ($event->tipoEntrega === 'delivery' && in_array($event->toStatus, ['listo', 'en_ruta'])) {
            $roles[] = 'repartidor';
        }

        SendPushNotificationJob::dispatch(
            $event->empresaId,
            $roles,
            $payload,
            $event->orderId,
            'order_status_' . $event->toStatus
        );
    }
}

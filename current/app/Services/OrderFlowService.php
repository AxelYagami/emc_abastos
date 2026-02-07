<?php

namespace App\Services;

use App\Events\OrderStatusChanged;
use App\Models\Orden;
use Illuminate\Support\Facades\DB;

class OrderFlowService
{
    /**
     * Status flow maps por tipo de entrega.
     * Cada status apunta al siguiente en la cadena.
     */
    private const PICKUP_FLOW = [
        'nuevo'      => 'confirmado',
        'confirmado' => 'preparando',
        'preparando' => 'listo',
        'listo'      => 'entregado',
    ];

    private const DELIVERY_FLOW = [
        'nuevo'      => 'confirmado',
        'confirmado' => 'preparando',
        'preparando' => 'listo',
        'listo'      => 'en_ruta',
        'en_ruta'    => 'entregado',
    ];

    private const ALL_STATUSES = [
        'nuevo', 'confirmado', 'preparando', 'listo', 'en_ruta', 'entregado', 'cancelado',
    ];

    private const STATUS_LABELS = [
        'nuevo'      => 'Nuevo',
        'confirmado' => 'Confirmado',
        'preparando' => 'Preparando',
        'listo'      => 'Listo',
        'en_ruta'    => 'En ruta',
        'entregado'  => 'Entregado',
        'cancelado'  => 'Cancelado',
    ];

    private const TIMESTAMP_MAP = [
        'confirmado' => 'confirmed_at',
        'preparando' => 'preparing_at',
        'listo'      => 'ready_at',
        'en_ruta'    => 'en_ruta_at',
        'entregado'  => 'delivered_at',
        'cancelado'  => 'cancelled_at',
    ];

    public static function allStatuses(): array
    {
        return self::ALL_STATUSES;
    }

    public static function statusLabel(string $status): string
    {
        return self::STATUS_LABELS[$status] ?? ucfirst($status);
    }

    public static function statusLabels(): array
    {
        return self::STATUS_LABELS;
    }

    /**
     * Retorna los pasos del flujo segun tipo de entrega.
     */
    public static function stepsFor(Orden $orden): array
    {
        $isDelivery = ($orden->tipo_entrega ?? $orden->fulfillment_type ?? 'pickup') === 'delivery';

        return $isDelivery
            ? ['nuevo', 'confirmado', 'preparando', 'listo', 'en_ruta', 'entregado']
            : ['nuevo', 'confirmado', 'preparando', 'listo', 'entregado'];
    }

    /**
     * Determina el siguiente status valido para una orden.
     * Retorna null si la orden ya esta en estado terminal.
     */
    public function nextStatus(Orden $orden): ?string
    {
        if (in_array($orden->status, ['entregado', 'cancelado'])) {
            return null;
        }

        $isDelivery = ($orden->tipo_entrega ?? $orden->fulfillment_type ?? 'pickup') === 'delivery';
        $flow = $isDelivery ? self::DELIVERY_FLOW : self::PICKUP_FLOW;

        return $flow[$orden->status] ?? null;
    }

    /**
     * Verifica si una orden necesita repartidor antes de avanzar.
     * Solo aplica delivery: para ir de 'listo' a 'en_ruta' se requiere repartidor.
     */
    public function needsRepartidor(Orden $orden): bool
    {
        $isDelivery = ($orden->tipo_entrega ?? $orden->fulfillment_type ?? 'pickup') === 'delivery';
        if (!$isDelivery) return false;

        return $orden->status === 'listo' && empty($orden->repartidor_id);
    }

    /**
     * Aplica transicion de estado. Valida empresa_id, guarda timestamps,
     * registra historial y dispara evento.
     */
    public function applyTransition(Orden $orden, string $toStatus, int $actorUserId, ?string $nota = null): bool
    {
        $fromStatus = $orden->status;

        if ($toStatus === $fromStatus) return false;
        if (!in_array($toStatus, self::ALL_STATUSES)) return false;

        $orden->status = $toStatus;

        // Guardar timestamp de transicion
        $tsCol = self::TIMESTAMP_MAP[$toStatus] ?? null;
        if ($tsCol && !$orden->{$tsCol}) {
            $orden->{$tsCol} = now();
        }

        $orden->save();

        // Historial
        DB::table('orden_status_histories')->insert([
            'empresa_id'       => $orden->empresa_id,
            'orden_id'         => $orden->id,
            'from_status'      => $fromStatus,
            'to_status'        => $toStatus,
            'actor_usuario_id' => $actorUserId,
            'nota'             => $nota,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // Disparar evento (push + whatsapp)
        event(new OrderStatusChanged(
            $orden->empresa_id,
            $orden->id,
            $orden->tipo_entrega ?? $orden->fulfillment_type ?? 'pickup',
            $fromStatus,
            $toStatus,
            $actorUserId
        ));

        return true;
    }

    /**
     * Asigna un repartidor a la orden.
     */
    public function assignRepartidor(Orden $orden, int $repartidorId, string $nombre): void
    {
        $orden->update([
            'repartidor_id'     => $repartidorId,
            'repartidor_nombre' => $nombre,
        ]);
    }
}

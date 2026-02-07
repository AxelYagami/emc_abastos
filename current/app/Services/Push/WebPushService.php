<?php

namespace App\Services\Push;

use App\Models\PushNotificationLog;
use App\Models\PushSubscription;
use Illuminate\Support\Facades\Log;

class WebPushService
{
    /**
     * Envia push notification a usuarios especificos de una empresa.
     */
    public function sendToUsers(int $empresaId, array $userIds, array $payload, ?int $orderId = null, ?string $eventKey = null): int
    {
        $subscriptions = PushSubscription::forEmpresa($empresaId)
            ->active()
            ->whereIn('user_id', $userIds)
            ->get();

        return $this->sendToSubscriptions($subscriptions, $payload, $empresaId, $orderId, $eventKey);
    }

    /**
     * Envia push a todos los usuarios de una empresa con cierto rol.
     */
    public function sendToRole(int $empresaId, array $roleSlugs, array $payload, ?int $orderId = null, ?string $eventKey = null): int
    {
        $userIds = \Illuminate\Support\Facades\DB::table('empresa_usuario')
            ->join('roles', 'roles.id', '=', 'empresa_usuario.rol_id')
            ->where('empresa_usuario.empresa_id', $empresaId)
            ->where('empresa_usuario.activo', true)
            ->whereIn('roles.slug', $roleSlugs)
            ->pluck('empresa_usuario.usuario_id')
            ->unique()
            ->all();

        if (empty($userIds)) return 0;

        return $this->sendToUsers($empresaId, $userIds, $payload, $orderId, $eventKey);
    }

    /**
     * Envia push a todas las suscripciones activas de una empresa.
     */
    public function sendToEmpresa(int $empresaId, array $payload, ?int $orderId = null, ?string $eventKey = null): int
    {
        $subscriptions = PushSubscription::forEmpresa($empresaId)->active()->get();

        return $this->sendToSubscriptions($subscriptions, $payload, $empresaId, $orderId, $eventKey);
    }

    /**
     * Core: envia a una coleccion de suscripciones.
     */
    private function sendToSubscriptions($subscriptions, array $payload, int $empresaId, ?int $orderId, ?string $eventKey): int
    {
        if ($subscriptions->isEmpty()) return 0;

        $sent = 0;

        // Intentar usar minishlink/web-push si esta disponible
        if (class_exists(\Minishlink\WebPush\WebPush::class)) {
            $sent = $this->sendViaLibrary($subscriptions, $payload, $empresaId, $orderId, $eventKey);
        } else {
            // Fallback: loguear que la libreria no esta instalada
            Log::warning('WebPush: minishlink/web-push no instalado. Push notifications deshabilitadas.');
            foreach ($subscriptions as $sub) {
                PushNotificationLog::create([
                    'empresa_id'   => $empresaId,
                    'user_id'      => $sub->user_id,
                    'order_id'     => $orderId,
                    'event_key'    => $eventKey,
                    'payload_json' => $payload,
                    'status'       => 'failed',
                    'error'        => 'minishlink/web-push no instalado',
                ]);
            }
        }

        return $sent;
    }

    private function sendViaLibrary($subscriptions, array $payload, int $empresaId, ?int $orderId, ?string $eventKey): int
    {
        $vapid = config('webpush.vapid');

        if (empty($vapid['public_key']) || empty($vapid['private_key'])) {
            Log::warning('WebPush: VAPID keys no configuradas.');
            return 0;
        }

        $auth = [
            'VAPID' => [
                'subject'    => $vapid['subject'],
                'publicKey'  => $vapid['public_key'],
                'privateKey' => $vapid['private_key'],
            ],
        ];

        $webPush = new \Minishlink\WebPush\WebPush($auth);
        $payloadJson = json_encode($payload);
        $sent = 0;

        foreach ($subscriptions as $sub) {
            $subscription = \Minishlink\WebPush\Subscription::create([
                'endpoint'        => $sub->endpoint,
                'publicKey'       => $sub->p256dh,
                'authToken'       => $sub->auth,
                'contentEncoding' => $sub->content_encoding,
            ]);

            $webPush->queueNotification($subscription, $payloadJson);
        }

        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getEndpoint();
            $sub = $subscriptions->first(fn($s) => $s->endpoint === $endpoint);

            if ($report->isSuccess()) {
                PushNotificationLog::create([
                    'empresa_id'   => $empresaId,
                    'user_id'      => $sub?->user_id,
                    'order_id'     => $orderId,
                    'event_key'    => $eventKey,
                    'payload_json' => $payload,
                    'status'       => 'sent',
                ]);
                $sub?->update(['last_used_at' => now()]);
                $sent++;
            } else {
                $reason = $report->getReason();
                $statusCode = $report->getResponse()?->getStatusCode();

                // Endpoint invalido: revocar suscripcion
                if (in_array($statusCode, [404, 410])) {
                    $sub?->revoke();
                }

                PushNotificationLog::create([
                    'empresa_id'   => $empresaId,
                    'user_id'      => $sub?->user_id,
                    'order_id'     => $orderId,
                    'event_key'    => $eventKey,
                    'payload_json' => $payload,
                    'status'       => 'failed',
                    'error'        => mb_substr((string) $reason, 0, 500),
                ]);
            }
        }

        return $sent;
    }
}

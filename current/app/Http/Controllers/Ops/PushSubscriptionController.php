<?php

namespace App\Http\Controllers\Ops;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function vapidPublicKey(): JsonResponse
    {
        return response()->json([
            'key' => config('webpush.vapid.public_key'),
        ]);
    }

    public function subscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => 'required|url|max:500',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
            'content_encoding' => 'nullable|string|max:20',
        ]);

        $empresaId = currentEmpresaId();
        $userId = auth()->id();
        $endpointHash = hash('sha256', $data['endpoint']);

        PushSubscription::updateOrCreate(
            [
                'empresa_id'    => $empresaId,
                'user_id'       => $userId,
                'endpoint_hash' => $endpointHash,
            ],
            [
                'endpoint'         => $data['endpoint'],
                'p256dh'           => $data['keys']['p256dh'],
                'auth'             => $data['keys']['auth'],
                'content_encoding' => $data['content_encoding'] ?? 'aesgcm',
                'user_agent'       => mb_substr($request->userAgent() ?? '', 0, 255),
                'revoked_at'       => null,
                'last_used_at'     => now(),
            ]
        );

        return response()->json(['ok' => true]);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => 'required|string',
        ]);

        $endpointHash = hash('sha256', $data['endpoint']);

        PushSubscription::where('empresa_id', currentEmpresaId())
            ->where('user_id', auth()->id())
            ->where('endpoint_hash', $endpointHash)
            ->update(['revoked_at' => now()]);

        return response()->json(['ok' => true]);
    }
}

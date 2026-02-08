@extends('layouts.admin', ['title'=>'WhatsApp','header'=>'Configuracion WhatsApp'])

@section('content')
<div class="space-y-6">
    {{-- Provider Configuration --}}
    <div class="bg-white border rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Proveedor de WhatsApp</h2>
                <p class="text-sm text-gray-500">Configura como se envian las notificaciones</p>
            </div>
            @php
                $currentProvider = $empresa->getSetting('whatsapp_provider') ?? env('WHATSAPP_PROVIDER', 'link');
                $apiKey = $empresa->getSetting('whatsapp_api_key');
            @endphp
            <span class="px-3 py-1 text-sm font-medium rounded-full {{ $currentProvider === 'link' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                {{ $currentProvider === 'link' ? 'Manual (Links)' : ucfirst($currentProvider) }}
            </span>
        </div>

        <form method="POST" action="{{ route('admin.whatsapp.config.update') }}" class="space-y-4">
            @csrf
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                    <select name="whatsapp_provider" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="link" {{ $currentProvider === 'link' ? 'selected' : '' }}>Link Manual (wa.me) - Sin costo</option>
                        <option value="callmebot" {{ $currentProvider === 'callmebot' ? 'selected' : '' }}>CallMeBot API - Gratis</option>
                        <option value="twilio" {{ $currentProvider === 'twilio' ? 'selected' : '' }}>Twilio - Pago por mensaje</option>
                        <option value="waha" {{ $currentProvider === 'waha' ? 'selected' : '' }}>WAHA Self-hosted - Gratis</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        <strong>Link Manual:</strong> Genera links wa.me para enviar manualmente<br>
                        <strong>CallMeBot:</strong> API gratuita, requiere activacion por usuario<br>
                        <strong>Twilio:</strong> API empresarial, costo por mensaje<br>
                        <strong>WAHA:</strong> Auto-hospedado, requiere servidor propio
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">API Key (si aplica)</label>
                    <input type="text" name="whatsapp_api_key" value="{{ $apiKey }}" 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500"
                           placeholder="Solo para CallMeBot o proveedores que lo requieran">
                    <p class="text-xs text-gray-500 mt-1">
                        Para CallMeBot: El usuario debe enviar "I allow callmebot to send me messages" al +34 644 52 74 88
                    </p>
                </div>
            </div>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                Guardar configuracion
            </button>
        </form>
    </div>

    {{-- Vendor Numbers --}}
    <div class="bg-white border rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Numeros del vendedor</h2>
                <p class="text-sm text-gray-500">Reciben notificaciones cuando hay nuevas ordenes</p>
            </div>
            <a class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-800 transition" href="{{ route('admin.whatsapp.create') }}">
                + Agregar numero
            </a>
        </div>

        @if($numeros->count() > 0)
        <div class="border rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left p-3 font-medium text-gray-600">WhatsApp</th>
                        <th class="text-center p-3 font-medium text-gray-600">Estado</th>
                        <th class="text-right p-3 font-medium text-gray-600">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($numeros as $n)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3">
                            <span class="font-medium">{{ $n->whatsapp }}</span>
                            @if($n->nombre)
                            <span class="text-gray-500 text-xs ml-2">({{ $n->nombre }})</span>
                            @endif
                        </td>
                        <td class="p-3 text-center">
                            @if($n->activo)
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">Activo</span>
                            @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">Inactivo</span>
                            @endif
                        </td>
                        <td class="p-3 text-right space-x-2">
                            <a href="https://wa.me/52{{ preg_replace('/\D/', '', $n->whatsapp) }}?text=Prueba%20de%20conexion" 
                               target="_blank"
                               class="text-green-600 hover:underline text-xs">Probar</a>
                            <form method="POST" action="{{ route('admin.whatsapp.toggle', $n->id) }}" class="inline">
                                @csrf
                                <button class="text-blue-600 hover:underline text-xs">{{ $n->activo ? 'Desactivar' : 'Activar' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.whatsapp.destroy', $n->id) }}" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline text-xs" onclick="return confirm('Eliminar este numero?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
            <p>No hay numeros configurados</p>
            <p class="text-xs mt-1">Agrega numeros para recibir notificaciones de ordenes</p>
        </div>
        @endif
    </div>

    {{-- Recent Logs --}}
    <div class="bg-white border rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Historial de envios</h2>
                <p class="text-sm text-gray-500">Ultimas notificaciones enviadas</p>
            </div>
        </div>

        @if($logs->count() > 0)
        <div class="border rounded-lg overflow-hidden">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left p-2 font-medium text-gray-600">Fecha</th>
                        <th class="text-left p-2 font-medium text-gray-600">Evento</th>
                        <th class="text-left p-2 font-medium text-gray-600">Destinatario</th>
                        <th class="text-center p-2 font-medium text-gray-600">Estado</th>
                        <th class="text-right p-2 font-medium text-gray-600">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($logs as $l)
                    <tr class="hover:bg-gray-50">
                        <td class="p-2 text-gray-500">{{ $l->created_at->format('d/m H:i') }}</td>
                        <td class="p-2">
                            <span class="font-medium">{{ $l->evento }}</span>
                            @if($l->orden_id)
                            <span class="text-gray-400 ml-1">#{{ $l->orden?->folio ?? $l->orden_id }}</span>
                            @endif
                        </td>
                        <td class="p-2">{{ $l->to_whatsapp }}</td>
                        <td class="p-2 text-center">
                            @if($l->status === 'sent')
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full">Enviado</span>
                            @elseif($l->status === 'failed')
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full" title="{{ $l->error }}">Fallido</span>
                            @elseif($l->status === 'skipped')
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full" title="{{ $l->skipped_reason }}">Omitido</span>
                            @else
                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full">{{ ucfirst($l->status) }}</span>
                            @endif
                        </td>
                        <td class="p-2 text-right">
                            @if($l->status === 'failed')
                            <form method="POST" action="{{ route('admin.whatsapp.retry', $l->id) }}" class="inline">
                                @csrf
                                <button class="text-blue-600 hover:underline">Reintentar</button>
                            </form>
                            @endif
                            @php
                                $payload = $l->payload ?? [];
                                $message = $payload['text'] ?? '';
                                $link = "https://wa.me/52" . preg_replace('/\D/', '', $l->to_whatsapp) . "?text=" . urlencode($message);
                            @endphp
                            <a href="{{ $link }}" target="_blank" class="text-green-600 hover:underline ml-2">Enviar manual</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <p>No hay envios registrados</p>
        </div>
        @endif
    </div>
</div>
@endsection

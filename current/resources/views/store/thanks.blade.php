@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-12 text-center">
    @php
        $statusConfig = [
            'success' => ['icon' => 'check', 'bg' => 'bg-green-100', 'text' => 'text-green-600', 'border' => 'border-green-200'],
            'failure' => ['icon' => 'x', 'bg' => 'bg-red-100', 'text' => 'text-red-600', 'border' => 'border-red-200'],
            'pending' => ['icon' => 'clock', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-600', 'border' => 'border-yellow-200'],
        ];
        $config = $statusConfig[$status ?? 'success'] ?? $statusConfig['success'];
    @endphp

    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="w-20 h-20 {{ $config['bg'] }} rounded-full flex items-center justify-center mx-auto mb-6">
            @if(($status ?? 'success') === 'success')
                <svg class="w-10 h-10 {{ $config['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            @elseif($status === 'failure')
                <svg class="w-10 h-10 {{ $config['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            @else
                <svg class="w-10 h-10 {{ $config['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            @endif
        </div>

        <h1 class="text-2xl font-bold text-gray-800 mb-2">
            @if(($status ?? 'success') === 'success')
                Gracias por tu pedido
            @elseif($status === 'failure')
                Hubo un problema con tu pago
            @else
                Pago pendiente
            @endif
        </h1>

        <p class="text-gray-600 mb-6">{{ $message ?? 'Tu pedido ha sido recibido correctamente.' }}</p>

        @if(isset($orden))
        <div class="{{ $config['bg'] }} {{ $config['border'] }} border rounded-lg p-4 mb-6">
            <div class="text-sm text-gray-600 mb-1">Numero de pedido</div>
            <div class="text-xl font-bold {{ $config['text'] }}">{{ $orden->folio }}</div>
        </div>

        <div class="text-left space-y-3 mb-6">
            <div class="flex justify-between py-2 border-b">
                <span class="text-gray-500">Cliente</span>
                <span class="font-medium">{{ $orden->comprador_nombre }}</span>
            </div>
            <div class="flex justify-between py-2 border-b">
                <span class="text-gray-500">WhatsApp</span>
                <span class="font-medium">{{ $orden->comprador_whatsapp }}</span>
            </div>
            <div class="flex justify-between py-2 border-b">
                <span class="text-gray-500">Entrega</span>
                <span class="font-medium">{{ $orden->tipo_entrega === 'delivery' ? 'A domicilio' : 'Recoger en tienda' }}</span>
            </div>
            <div class="flex justify-between py-2 border-b">
                <span class="text-gray-500">Total</span>
                <span class="font-bold text-lg text-green-600">${{ number_format($orden->total, 2) }}</span>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-600">
                Te enviaremos un mensaje por WhatsApp cuando tu pedido este listo.
                Puedes dar seguimiento a tu pedido con el numero <strong>{{ $orden->folio }}</strong>
            </p>
        </div>
        @endif

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            @if(isset($orden))
            <a href="{{ route('store.track', $orden->folio) }}"
               class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                Ver estado del pedido
            </a>
            @endif
            <a href="{{ route('store.home') }}"
               class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                Seguir comprando
            </a>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
@php
    $cart = session('cart', []);
    $items = [];
    $total = 0;
    if (!empty($cart)) {
        $productos = \App\Models\Producto::whereIn('id', array_keys($cart))->get()->keyBy('id');
        foreach ($cart as $pid => $qty) {
            $p = $productos->get((int)$pid);
            if (!$p) continue;
            $items[] = ['producto' => $p, 'qty' => (int)$qty];
            $total += ((float)$p->precio) * (int)$qty;
        }
    }
@endphp

<div class="max-w-4xl mx-auto py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Finalizar Compra</h1>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Form -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('checkout.place') }}" class="space-y-6">
                @csrf

                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="font-semibold text-gray-800 mb-4">Datos de Contacto</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo *</label>
                            <input type="text" name="comprador_nombre" value="{{ old('comprador_nombre', auth()->user()?->name) }}" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            @error('comprador_nombre')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp *</label>
                            <input type="tel" name="comprador_whatsapp" value="{{ old('comprador_whatsapp', auth()->user()?->whatsapp) }}" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                   placeholder="81 1234 5678">
                            @error('comprador_whatsapp')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email (opcional)</label>
                            <input type="email" name="comprador_email" value="{{ old('comprador_email', auth()->user()?->email) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="font-semibold text-gray-800 mb-4">Entrega</h2>

                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="tipo_entrega" value="pickup" checked class="text-green-600">
                            <div>
                                <div class="font-medium">Recoger en tienda</div>
                                <div class="text-sm text-gray-500">Pasa por tu pedido cuando este listo</div>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="tipo_entrega" value="delivery" class="text-green-600">
                            <div>
                                <div class="font-medium">Envio a domicilio</div>
                                <div class="text-sm text-gray-500">Te contactaremos para coordinar el envio</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="font-semibold text-gray-800 mb-4">Metodo de Pago</h2>

                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="metodo_pago" value="efectivo" checked class="text-green-600">
                            <div>
                                <div class="font-medium">Pago en efectivo</div>
                                <div class="text-sm text-gray-500">Paga al recoger o recibir tu pedido</div>
                            </div>
                        </label>

                        @if($hasMercadoPago ?? false)
                        <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="metodo_pago" value="mercadopago" class="text-green-600">
                            <div class="flex-1">
                                <div class="font-medium flex items-center gap-2">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="#009EE3">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                    </svg>
                                    MercadoPago
                                </div>
                                <div class="text-sm text-gray-500">Paga con tarjeta, transferencia o saldo</div>
                            </div>
                        </label>
                        @endif
                    </div>
                </div>

                <button type="submit"
                        class="w-full py-4 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition text-lg">
                    Confirmar Pedido
                </button>
            </form>
        </div>

        <!-- Order Summary -->
        <div>
            <div class="bg-white rounded-lg shadow p-6 sticky top-24">
                <h2 class="font-semibold text-gray-800 mb-4">Resumen del Pedido</h2>

                <div class="space-y-3 mb-4">
                    @foreach($items as $item)
                    <div class="flex justify-between text-sm">
                        <div>
                            <span class="text-gray-600">{{ $item['producto']->nombre }}</span>
                            <span class="text-gray-400">x{{ $item['qty'] }}</span>
                        </div>
                        <span class="font-medium">${{ number_format($item['producto']->precio * $item['qty'], 2) }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="border-t pt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">${{ number_format($total, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-gray-600">Envio</span>
                        <span class="text-gray-500 text-sm">Por definir</span>
                    </div>
                    <div class="flex justify-between items-center mt-4 pt-4 border-t">
                        <span class="text-lg font-bold">Total</span>
                        <span class="text-2xl font-bold text-green-600">${{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <a href="{{ route('cart.index') }}" class="block text-center text-sm text-gray-500 hover:text-gray-700 mt-4">
                    Modificar carrito
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

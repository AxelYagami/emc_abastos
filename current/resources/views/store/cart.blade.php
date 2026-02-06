@extends('layouts.store')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
  <h1 class="text-3xl font-bold text-gray-800 mb-8">Mi Carrito</h1>

  @if(session('cart') && count(session('cart')))
    <div class="grid lg:grid-cols-3 gap-8">
      <!-- Cart Items -->
      <div class="lg:col-span-2 space-y-4">
        @foreach($items as $it)
          <div class="bg-white rounded-xl shadow-sm border p-4 flex gap-4" x-data="{ qty: {{ $it['qty'] }} }">
            <!-- Product Image -->
            <div class="w-24 h-24 bg-gray-100 rounded-lg flex-shrink-0 overflow-hidden">
              @if($it['producto']->imagen_url)
                <img src="{{ $it['producto']->imagen_url }}" alt="{{ $it['producto']->nombre }}" class="w-full h-full object-cover">
              @else
                <div class="w-full h-full flex items-center justify-center text-gray-300">
                  <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                </div>
              @endif
            </div>

            <!-- Product Info -->
            <div class="flex-1">
              <h3 class="font-semibold text-gray-800">{{ $it['producto']->nombre }}</h3>
              <p class="text-primary-600 font-bold text-lg">${{ number_format($it['producto']->precio, 2) }}</p>

              <!-- Quantity Controls -->
              <form method="POST" action="{{ route('cart.update') }}" class="mt-3 flex items-center gap-3">
                @csrf
                <input type="hidden" name="producto_id" value="{{ $it['producto']->id }}">
                <div class="flex items-center border rounded-lg">
                  <button type="button" @click="qty = Math.max(0, qty - 1)" class="px-3 py-2 text-gray-600 hover:bg-gray-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                  </button>
                  <input type="number" name="qty" x-model="qty" min="0" max="99" class="w-16 text-center border-0 focus:ring-0 font-semibold">
                  <button type="button" @click="qty = Math.min(99, qty + 1)" class="px-3 py-2 text-gray-600 hover:bg-gray-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                  </button>
                </div>
                <button type="submit" class="text-sm text-primary-600 hover:text-primary-700 font-medium">Actualizar</button>
              </form>
            </div>

            <!-- Subtotal -->
            <div class="text-right">
              <p class="text-sm text-gray-500">Subtotal</p>
              <p class="font-bold text-lg">${{ number_format($it['producto']->precio * $it['qty'], 2) }}</p>
            </div>
          </div>
        @endforeach

        <!-- Clear Cart -->
        <div class="flex justify-end">
          <form method="POST" action="{{ route('cart.clear') }}">
            @csrf
            <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium flex items-center gap-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
              Vaciar carrito
            </button>
          </form>
        </div>
      </div>

      <!-- Order Summary (Sticky) -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border p-6 sticky top-24">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Resumen del pedido</h2>

          <div class="space-y-3 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-600">Subtotal ({{ count($items) }} productos)</span>
              <span class="font-medium">${{ number_format($total, 2) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Envío</span>
              <span class="text-primary-600 font-medium">Por calcular</span>
            </div>
            <div class="border-t pt-3 mt-3">
              <div class="flex justify-between text-lg">
                <span class="font-bold">Total</span>
                <span class="font-bold text-primary-600">${{ number_format($total, 2) }}</span>
              </div>
            </div>
          </div>

          <div class="mt-6 space-y-3">
            <a href="{{ route('checkout.show') }}" class="block w-full bg-primary-600 hover:bg-primary-700 text-white text-center py-3 rounded-lg font-semibold transition">
              Proceder al pago
            </a>
            <a href="{{ route('store.home') }}" class="block w-full border border-gray-300 hover:bg-gray-50 text-gray-700 text-center py-3 rounded-lg font-medium transition">
              Seguir comprando
            </a>
          </div>

          <!-- Trust Badges -->
          <div class="mt-6 pt-6 border-t">
            <div class="flex items-center gap-2 text-sm text-gray-500">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
              </svg>
              Compra segura garantizada
            </div>
          </div>
        </div>
      </div>
    </div>
  @else
    <!-- Empty Cart -->
    <div class="text-center py-16">
      <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
      </div>
      <h2 class="text-2xl font-bold text-gray-800 mb-2">Tu carrito está vacío</h2>
      <p class="text-gray-500 mb-6">Agrega productos para comenzar tu compra</p>
      <a href="{{ route('store.home') }}" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-semibold transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Ver productos
      </a>
    </div>
  @endif
</div>
@endsection

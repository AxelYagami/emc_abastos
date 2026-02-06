@extends('layouts.app', ['title' => $producto->nombre])

@section('content')
<div class="bg-white border rounded p-6">
  <div class="text-xs text-gray-500 mb-2">
    <a class="hover:underline" href="{{ route('store.home') }}">Tienda</a>
    <span class="mx-2">/</span>
    <span>{{ $producto->nombre }}</span>
  </div>

  <div class="grid md:grid-cols-2 gap-6">
    <div class="bg-gray-50 border rounded aspect-video flex items-center justify-center text-gray-400">
      @if($producto->imagen_url)
        <img src="{{ $producto->imagen_url }}" class="w-full h-full object-cover rounded" alt="{{ $producto->nombre }}">
      @else
        Sin imagen
      @endif
    </div>

    <div>
      <h1 class="text-2xl font-bold">{{ $producto->nombre }}</h1>
      <div class="text-sm text-gray-500 mt-1">{{ $producto->categoria?->nombre }}</div>
      <div class="mt-4 text-3xl font-bold">${{ number_format($producto->precio,2) }}</div>
      @if($producto->descripcion)
        <p class="mt-4 text-gray-700">{{ $producto->descripcion }}</p>
      @endif

      <form method="POST" action="{{ route('cart.add') }}" class="mt-6">
        @csrf
        <input type="hidden" name="producto_id" value="{{ $producto->id }}">
        <div class="flex gap-2">
          <input type="number" name="qty" value="1" min="1" max="99" class="w-24 border rounded px-2 py-2">
          <button class="flex-1 bg-black text-white rounded py-2">Agregar al carrito</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

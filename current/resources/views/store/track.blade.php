@extends('layouts.store')

@section('content')
<div class="max-w-3xl mx-auto p-4">
  <h1 class="text-xl font-semibold mb-4">Seguimiento de pedido</h1>

  <div class="border rounded p-4 space-y-2">
    <div class="text-sm"><span class="opacity-70">Folio:</span> <span class="font-medium">{{ $orden->folio }}</span></div>
    <div class="text-sm"><span class="opacity-70">Status:</span> <span class="font-medium">{{ $orden->status }}</span></div>
    <div class="text-sm"><span class="opacity-70">Total:</span> <span class="font-medium">${{ number_format($orden->total,2) }}</span></div>

    {{-- Privacy: do not expose PII publicly --}}
    <div class="text-xs opacity-70 pt-2">
      Por privacidad, no mostramos nombre ni WhatsApp en esta pantalla pública.
    </div>
  </div>

  <div class="mt-4">
    <a class="underline text-sm" href="{{ route('store.home') }}">Volver a la tienda</a>
  </div>
</div>
@endsection

@extends('layouts.admin', ['title'=>'Orden', 'header'=>'Detalle de Orden'])
@section('content')
<div class="mb-4 p-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-sm flex items-center justify-between">
    <span>Modo consulta. Para operar esta orden usa el panel movil.</span>
    <a href="{{ url('/ops/movil/orden/' . $orden->id) }}" class="px-3 py-1 bg-amber-600 text-white rounded text-xs font-medium hover:bg-amber-700">Abrir en Ops Movil</a>
</div>
<div class="grid lg:grid-cols-3 gap-4">
  <div class="lg:col-span-2 bg-white border rounded p-4">
    <div class="flex items-start justify-between">
      <div>
        <div class="text-xs text-gray-500">Orden</div>
        <div class="text-xl font-bold font-mono">{{ $orden->folio ?? ('#'.$orden->id) }}</div>
        <div class="text-sm text-gray-600 mt-1">{{ $orden->comprador_nombre }} · {{ $orden->comprador_whatsapp }}</div>
      </div>
      <div class="text-right">
        <div class="text-xs text-gray-500">Total</div>
        <div class="text-2xl font-bold">${{ number_format($orden->total,2) }}</div>
      </div>
    </div>

    <h2 class="mt-6 font-semibold">Items</h2>
    <div class="mt-2 text-sm divide-y">
      @foreach($orden->items as $it)
        <div class="py-2 flex justify-between">
          <div>{{ $it->nombre ?? $it->producto?->nombre }} <span class="text-gray-500">x{{ $it->cantidad }}</span></div>
          <div class="font-medium">${{ number_format($it->total,2) }}</div>
        </div>
      @endforeach
    </div>

    <h2 class="mt-6 font-semibold">Pagos</h2>
    <div class="mt-2">
      <div class="border rounded overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="text-left p-3">Fecha</th>
              <th class="text-left p-3">Metodo</th>
              <th class="text-right p-3">Monto</th>
              <th class="text-left p-3">Status</th>
              <th class="text-left p-3">Ref</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @foreach($orden->pagos as $p)
              <tr>
                <td class="p-3 text-xs text-gray-500">{{ $p->created_at }}</td>
                <td class="p-3">{{ $p->metodo }}</td>
                <td class="p-3 text-right font-bold">${{ number_format($p->monto,2) }}</td>
                <td class="p-3">{{ $p->status }}</td>
                <td class="p-3 text-xs">{{ $p->referencia }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="bg-white border rounded p-4">
    <div class="font-semibold">Estatus</div>
    <div class="mt-2 text-sm">
      Actual: <span class="font-semibold">{{ $orden->status }}</span>
    </div>
    <p class="mt-3 text-xs text-gray-400">Las acciones operativas se manejan desde Ops Movil.</p>
  </div>
</div>
@endsection

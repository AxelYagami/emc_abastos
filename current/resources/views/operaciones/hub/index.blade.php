@extends('layouts.admin', ['title'=>'Operaciones', 'header'=>'Centro de Operaciones'])
@section('content')
<div class="mb-4 p-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-sm flex items-center justify-between">
    <span>Modo consulta. Para operaciones en tiempo real usa el panel movil.</span>
    <a href="{{ route('ops.movil') }}" class="px-3 py-1 bg-amber-600 text-white rounded text-xs font-medium hover:bg-amber-700">Ir a Ops Movil</a>
</div>
<div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
  <a class="bg-black text-white border rounded-xl p-5 hover:bg-gray-800 transition" href="{{ route('ops.movil') }}">
    <div class="font-bold text-lg">Ops Movil</div>
    <div class="text-sm text-gray-300 mt-1">Panel operativo principal con stepper, metricas y push.</div>
  </a>
  <a class="bg-white border rounded-xl p-5 hover:bg-gray-50 transition" href="{{ route('ops.ordenes.hoy') }}">
    <div class="font-semibold">Lista del dia</div>
    <div class="text-xs text-gray-500 mt-1">Consulta de ordenes de hoy.</div>
  </a>
  <a class="bg-white border rounded-xl p-5 hover:bg-gray-50 transition" href="{{ route('ops.ordenes.index') }}">
    <div class="font-semibold">Ordenes</div>
    <div class="text-xs text-gray-500 mt-1">Busqueda y filtros historicos.</div>
  </a>
  <a class="bg-white border rounded-xl p-5 hover:bg-gray-50 transition" href="{{ route('ops.whatsapp.index') }}">
    <div class="font-semibold">WhatsApp</div>
    <div class="text-xs text-gray-500 mt-1">Logs de notificaciones.</div>
  </a>
</div>
@endsection

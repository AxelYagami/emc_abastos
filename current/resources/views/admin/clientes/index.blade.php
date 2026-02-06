@extends('layouts.admin', ['title'=>'Clientes','header'=>'Clientes'])

@section('content')
<div class="bg-white border rounded p-4 mb-4">
  <form class="flex gap-2" method="GET">
    <input name="q" value="{{ $search }}" class="border rounded px-3 py-2 w-72" placeholder="Nombre / WhatsApp / Email">
    <button class="px-4 py-2 bg-gray-900 text-white rounded">Buscar</button>
  </form>
</div>

<div class="bg-white border rounded overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-50 border-b">
      <tr>
        <th class="text-left p-3">Cliente</th>
        <th class="text-left p-3">WhatsApp</th>
        <th class="text-left p-3">Email</th>
        <th class="text-center p-3">Enviar estatus</th>
        <th class="p-3"></th>
      </tr>
    </thead>
    <tbody class="divide-y">
      @foreach($clientes as $c)
        <tr>
          <td class="p-3 font-medium">{{ $c->nombre }}</td>
          <td class="p-3">{{ $c->whatsapp }}</td>
          <td class="p-3">{{ $c->email }}</td>
          <td class="p-3 text-center">
            <form method="POST" action="{{ route('admin.clientes.toggle', $c->id) }}">
              @csrf
              <button class="px-3 py-1 rounded border {{ $c->enviar_estatus ? 'bg-green-50' : 'bg-gray-50' }}">
                {{ $c->enviar_estatus ? 'Sí' : 'No' }}
              </button>
            </form>
          </td>
          <td class="p-3 text-right">
            <a class="text-blue-700 hover:underline" href="{{ route('admin.clientes.show',$c->id) }}">Ver</a>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $clientes->links() }}</div>
@endsection

@extends('layouts.admin', ['title'=>'Categorías','header'=>'Categorías'])

@section('content')
<div class="flex items-center justify-between mb-4">
  <div class="text-sm text-gray-600">Organiza tu catálogo.</div>
  <a class="px-4 py-2 rounded bg-black text-white" href="{{ route('admin.categorias.create') }}">Nueva</a>
</div>

<div class="bg-white border rounded overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-50 border-b">
      <tr>
        <th class="text-left p-3">Nombre</th>
        <th class="text-left p-3">Slug</th>
        <th class="text-center p-3">Orden</th>
        <th class="text-center p-3">Activa</th>
        <th class="p-3"></th>
      </tr>
    </thead>
    <tbody class="divide-y">
      @foreach($categorias as $c)
        <tr>
          <td class="p-3 font-medium">{{ $c->nombre }}</td>
          <td class="p-3 text-gray-600">{{ $c->slug }}</td>
          <td class="p-3 text-center">{{ $c->orden }}</td>
          <td class="p-3 text-center">{{ $c->activa ? 'Sí' : 'No' }}</td>
          <td class="p-3 text-right">
            <a class="text-blue-700 hover:underline" href="{{ route('admin.categorias.edit',$c->id) }}">Editar</a>
            <form method="POST" action="{{ route('admin.categorias.destroy',$c->id) }}" class="inline">
              @csrf @method('DELETE')
              <button class="text-red-700 hover:underline ml-2" onclick="return confirm('¿Eliminar?')">Eliminar</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection

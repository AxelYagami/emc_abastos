@extends('layouts.admin', ['title' => 'Portales', 'header' => 'Gestion de Portales'])

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-4 border-b flex justify-between items-center">
        <h2 class="font-semibold text-gray-800">Portales</h2>
        <a href="{{ route('admin.portales.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">+ Nuevo Portal</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-left">Portal</th>
                    <th class="p-3 text-left">Dominio</th>
                    <th class="p-3 text-center">Empresas</th>
                    <th class="p-3 text-center">Estado</th>
                    <th class="p-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($portales as $p)
                <tr class="hover:bg-gray-50">
                    <td class="p-3">
                        <div class="flex items-center gap-3">
                            @if($p->getLogoUrl())
                            <img src="{{ $p->getLogoUrl() }}" class="w-10 h-10 rounded-lg object-cover">
                            @else
                            <div class="w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center text-gray-500">{{ substr($p->nombre, 0, 1) }}</div>
                            @endif
                            <div>
                                <div class="font-medium">{{ $p->nombre }}</div>
                                <div class="text-xs text-gray-500">{{ $p->slug }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="p-3 text-gray-600">{{ $p->dominio ?? '-' }}</td>
                    <td class="p-3 text-center"><span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">{{ $p->empresas_count }}</span></td>
                    <td class="p-3 text-center">
                        @if($p->activo)
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Activo</span>
                        @else
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs">Inactivo</span>
                        @endif
                    </td>
                    <td class="p-3 text-right space-x-2">
                        <a href="{{ route('admin.portales.edit', $p->id) }}" class="text-blue-600 hover:underline">Editar</a>
                        <form method="POST" action="{{ route('admin.portales.destroy', $p->id) }}" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline" onclick="return confirm('Eliminar portal?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-8 text-center text-gray-500">No hay portales</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

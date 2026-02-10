@extends('layouts.admin', ['title' => 'Editar Portal', 'header' => 'Editar Portal'])

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.portales.update', $portal->id) }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6 space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Portal *</label>
            <input type="text" name="nombre" value="{{ $portal->nombre }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                <input type="text" name="slug" value="{{ $portal->slug }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dominio</label>
                <input type="text" name="dominio" value="{{ $portal->dominio }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500" placeholder="ejemplo.com">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
            @if($portal->getLogoUrl())
            <div class="mb-2"><img src="{{ $portal->getLogoUrl() }}" class="h-16 rounded-lg"></div>
            @endif
            <input type="file" name="logo" accept="image/*" class="w-full px-4 py-2 border rounded-lg">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Color Primario</label>
                <input type="color" name="primary_color" value="{{ $portal->primary_color }}" class="w-full h-10 border rounded-lg cursor-pointer">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Color Secundario</label>
                <input type="color" name="secondary_color" value="{{ $portal->secondary_color }}" class="w-full h-10 border rounded-lg cursor-pointer">
            </div>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="activo" value="1" {{ $portal->activo ? 'checked' : '' }} id="activo" class="rounded text-primary-600">
            <label for="activo" class="text-sm text-gray-700">Portal activo</label>
        </div>
        <div class="flex gap-3 pt-4">
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Guardar</button>
            <a href="{{ route('admin.portales.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
        </div>
    </form>
</div>
@endsection

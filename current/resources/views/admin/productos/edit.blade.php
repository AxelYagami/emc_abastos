@extends('layouts.admin')

@section('content')
<div class="rounded-xl bg-white border p-5 max-w-3xl">
  <div class="text-xl font-semibold">Editar producto</div>

  <form method="POST" action="{{ route('admin.productos.update',$producto->id) }}" enctype="multipart/form-data" class="mt-4 space-y-4">
    @csrf
    @method('PUT')

    <div>
      <label class="text-sm text-slate-600">Nombre</label>
      <input class="mt-1 w-full rounded-lg border px-3 py-2" name="nombre" value="{{ old('nombre',$producto->nombre) }}" required>
      @error('nombre')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
      <div>
        <label class="text-sm text-slate-600">SKU</label>
        <input class="mt-1 w-full rounded-lg border px-3 py-2" name="sku" value="{{ old('sku',$producto->sku) }}">
      </div>
      <div>
        <label class="text-sm text-slate-600">Unidad</label>
        <input class="mt-1 w-full rounded-lg border px-3 py-2" name="unidad" value="{{ old('unidad',$producto->unidad) }}" placeholder="kg, lt, pza, etc.">
      </div>
    </div>

    <div>
      <label class="text-sm text-slate-600">Descripción</label>
      <textarea class="mt-1 w-full rounded-lg border px-3 py-2" rows="3" name="descripcion">{{ old('descripcion',$producto->descripcion) }}</textarea>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
      <div>
        <label class="text-sm text-slate-600">Categoría</label>
        <select class="mt-1 w-full rounded-lg border px-3 py-2" name="categoria_id">
          <option value="">—</option>
          @foreach($categorias as $c)
            <option value="{{ $c->id }}" @selected(old('categoria_id',$producto->categoria_id)==$c->id)>{{ $c->nombre }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="text-sm text-slate-600">Precio</label>
        <input class="mt-1 w-full rounded-lg border px-3 py-2" name="precio" value="{{ old('precio',$producto->precio) }}" required>
      </div>
    </div>

    <!-- Image Section -->
    <div class="border rounded-lg p-4 space-y-3">
      <label class="block text-sm font-medium mb-2">Imagen del Producto</label>

      @if($producto->imagen_path || $producto->imagen_url)
        <div class="mb-3">
          <p class="text-xs text-gray-600 mb-2">Imagen actual:</p>
          <img src="{{ $producto->display_image }}" alt="{{ $producto->nombre }}" class="w-32 h-32 object-cover rounded border">
        </div>
      @endif

      <div class="space-y-2">
        <label class="flex items-center gap-2">
          <input type="radio" name="image_source" value="auto" @checked(old('image_source', $producto->image_source ?? 'auto')==='auto')>
          <span class="text-sm">Automática (se busca según nombre + unidad + categoría)</span>
        </label>

        <label class="flex items-center gap-2">
          <input type="radio" name="image_source" value="manual" id="image_manual" @checked(old('image_source', $producto->image_source)==='manual')>
          <span class="text-sm">Manual (subir o URL)</span>
        </label>
      </div>

      <div id="manual_image_fields" class="space-y-3 pl-6" style="display: none;">
        <div>
          <label class="block text-xs text-gray-600 mb-1">Subir nueva imagen</label>
          <input type="file" name="imagen" accept="image/*" class="w-full border rounded p-2 text-sm">
          <p class="text-xs text-gray-500 mt-1">O proporciona una URL externa:</p>
        </div>

        <div>
          <label class="block text-xs text-gray-600 mb-1">URL de imagen externa</label>
          <input type="url" name="imagen_url" id="imagen_url_input" class="w-full border rounded p-2 text-sm" placeholder="https://ejemplo.com/imagen.jpg" value="{{ old('imagen_url', $producto->imagen_url) }}">
          <a href="#" onclick="event.preventDefault(); window.open('https://www.google.com/search?q={{ urlencode($producto->nombre) }}+fresco&tbm=isch', '_blank');" class="inline-block mt-2 text-xs text-blue-600 hover:underline">
            🔍 Buscar en Google Images
          </a>
        </div>
      </div>
    </div>

    <script>
      // Show/hide manual image fields
      document.querySelectorAll('input[name="image_source"]').forEach(radio => {
        radio.addEventListener('change', function() {
          document.getElementById('manual_image_fields').style.display =
            this.value === 'manual' ? 'block' : 'none';
        });
      });
      // Initialize on load
      if (document.getElementById('image_manual').checked) {
        document.getElementById('manual_image_fields').style.display = 'block';
      }
    </script>

    <div class="flex items-center gap-2">
      <input type="hidden" name="activo" value="0">
      <input type="checkbox" name="activo" value="1" @checked(old('activo',$producto->activo))>
      <span class="text-sm">Activo</span>
    </div>

    <div class="flex flex-wrap gap-2">
      <button type="submit" class="rounded-lg bg-emerald-600 text-white px-4 py-2">Guardar</button>
      <a class="rounded-lg border px-4 py-2" href="{{ route('admin.productos.index') }}">Volver</a>
    </div>
  </form>

  <!-- Formulario de eliminar (separado) -->
  <form method="POST" action="{{ route('admin.productos.destroy',$producto->id) }}" onsubmit="return confirm('¿Eliminar producto?')" class="mt-4">
    @csrf
    @method('DELETE')
    <button class="rounded-lg border border-red-600 text-red-600 hover:bg-red-50 px-4 py-2" type="submit">Eliminar producto</button>
  </form>
</div>
@endsection

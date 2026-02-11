@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto p-4">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">Crear producto</h1>
    <a class="text-sm underline" href="{{ route('admin.productos.index') }}">Volver</a>
  </div>

  @if ($errors->any())
    <div class="mb-4 rounded border p-3">
      <ul class="list-disc pl-5 text-sm">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.productos.store') }}" enctype="multipart/form-data" class="space-y-4">
    @csrf

    <div>
      <label class="block text-sm font-medium mb-1">Nombre</label>
      <input class="w-full border rounded p-2" name="nombre" value="{{ old('nombre') }}" required>
    </div>

    <div class="grid grid-cols-2 gap-3">
      <div>
        <label class="block text-sm font-medium mb-1">SKU</label>
        <input class="w-full border rounded p-2" name="sku" value="{{ old('sku') }}">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Unidad</label>
        <input class="w-full border rounded p-2" name="unidad" value="{{ old('unidad') }}" placeholder="kg, lt, pza, etc.">
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Categoría</label>
      <select class="w-full border rounded p-2" name="categoria_id">
        <option value="">—</option>
        @foreach(($categorias ?? []) as $c)
          <option value="{{ $c->id }}" @selected(old('categoria_id')==$c->id)>{{ $c->nombre }}</option>
        @endforeach
      </select>
    </div>

    <div class="grid grid-cols-2 gap-3">
      <div>
        <label class="block text-sm font-medium mb-1">Precio</label>
        <input class="w-full border rounded p-2" name="precio" value="{{ old('precio') }}" inputmode="decimal" required>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Stock (opcional)</label>
        <input class="w-full border rounded p-2" name="stock" value="{{ old('stock') }}" inputmode="numeric">
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Descripción</label>
      <textarea class="w-full border rounded p-2" rows="4" name="descripcion">{{ old('descripcion') }}</textarea>
    </div>

    <!-- Image Section -->
    <div class="border rounded p-4 space-y-3">
      <label class="block text-sm font-medium mb-2">Imagen del Producto</label>

      <div class="space-y-2">
        <label class="flex items-center gap-2">
          <input type="radio" name="image_source" value="auto" @checked(old('image_source', 'auto')==='auto')>
          <span class="text-sm">Automática (se busca según nombre + unidad + categoría)</span>
        </label>

        <label class="flex items-center gap-2">
          <input type="radio" name="image_source" value="manual" id="image_manual" @checked(old('image_source')==='manual')>
          <span class="text-sm">Manual (subir o URL)</span>
        </label>
      </div>

      <div id="manual_image_fields" class="space-y-3 pl-6" style="display: none;">
        <div>
          <label class="block text-xs text-gray-600 mb-1">Subir imagen</label>
          <input type="file" name="imagen" accept="image/*" class="w-full border rounded p-2 text-sm">
          <p class="text-xs text-gray-500 mt-1">O proporciona una URL externa:</p>
        </div>

        <div>
          <label class="block text-xs text-gray-600 mb-1">URL de imagen externa</label>
          <input type="url" name="imagen_url" class="w-full border rounded p-2 text-sm" placeholder="https://ejemplo.com/imagen.jpg" value="{{ old('imagen_url') }}">
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
      <!-- Fix: checkbox unchecked sends nothing -->
      <input type="hidden" name="activo" value="0">
      <input type="checkbox" name="activo" value="1" id="activo" @checked(old('activo',1)==1)>
      <label for="activo" class="text-sm">Activo</label>
    </div>

    <div class="pt-2">
      <button class="px-4 py-2 rounded bg-black text-white">Guardar</button>
    </div>
  </form>
</div>
@endsection

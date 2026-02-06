@csrf
<div class="grid md:grid-cols-2 gap-4">
  <div>
    <label class="text-xs text-gray-500">Nombre</label>
    <input name="nombre" value="{{ old('nombre', $producto->nombre ?? '') }}" class="w-full mt-1 border rounded px-3 py-2">
    @error('nombre')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
  </div>
  <div>
    <label class="text-xs text-gray-500">Categoría</label>
    <select name="categoria_id" class="w-full mt-1 border rounded px-3 py-2">
      <option value="">—</option>
      @foreach($categorias as $c)
        <option value="{{ $c->id }}" @selected((string)old('categoria_id', $producto->categoria_id ?? '')===(string)$c->id)>{{ $c->nombre }}</option>
      @endforeach
    </select>
  </div>

  <div>
    <label class="text-xs text-gray-500">Precio</label>
    <input name="precio" type="number" step="0.01" value="{{ old('precio', $producto->precio ?? '0') }}" class="w-full mt-1 border rounded px-3 py-2">
  </div>

  <div>
    <label class="text-xs text-gray-500">SKU</label>
    <input name="sku" value="{{ old('sku', $producto->sku ?? '') }}" class="w-full mt-1 border rounded px-3 py-2">
  </div>

  <div class="md:col-span-2">
    <label class="text-xs text-gray-500">Descripción</label>
    <textarea name="descripcion" rows="4" class="w-full mt-1 border rounded px-3 py-2">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
  </div>

  <div class="md:col-span-2">
    <label class="inline-flex items-center gap-2">
      <input type="hidden" name="activo" value="0">
      <input type="checkbox" name="activo" value="1" @checked((bool)old('activo', $producto->activo ?? true))>
      <span class="text-sm">Activo</span>
    </label>
  </div>
</div>

<div class="mt-5 flex gap-2">
  <button class="px-4 py-2 rounded bg-black text-white">Guardar</button>
  <a href="{{ route('admin.productos.index') }}" class="px-4 py-2 rounded border">Cancelar</a>
</div>

@extends('layouts.admin', ['title' => 'Editar Portal', 'header' => 'Editar: ' . $portal->nombre])

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.portales.update', $portal) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
            <ul class="list-disc list-inside text-red-700 text-sm">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Información Básica -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Información Básica</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Portal *</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $portal->nombre) }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug (URL)</label>
                    <input type="text" name="slug" value="{{ old('slug', $portal->slug) }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dominio</label>
                    <input type="text" name="dominio" value="{{ old('dominio', $portal->dominio) }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500"
                           placeholder="miportal.com">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tagline</label>
                    <input type="text" name="tagline" value="{{ old('tagline', $portal->tagline) }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripcion</label>
                    <textarea name="descripcion" rows="2"
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">{{ old('descripcion', $portal->descripcion) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                    @if($portal->logo_path)
                    <div class="mb-2">
                        <img src="{{ $portal->getLogoUrl() }}" alt="Logo actual" class="h-16 object-contain rounded border">
                    </div>
                    @endif
                    <input type="file" name="logo" accept="image/*"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="flex items-center">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="activo" value="1" {{ $portal->activo ? 'checked' : '' }}
                               class="w-4 h-4 text-primary-600 rounded">
                        <span class="text-sm text-gray-700">Portal Activo</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Template -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Template del Portal</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <label class="relative cursor-pointer">
                    <input type="radio" name="active_template" value="default" 
                           {{ ($portal->active_template ?? 'default') === 'default' ? 'checked' : '' }} class="peer sr-only">
                    <div class="border-2 rounded-xl p-4 transition-all peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-gray-300">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-800">Default</span>
                                <span class="ml-2 text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Clasico</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500">Hero con gradiente, secciones verticales.</p>
                    </div>
                </label>
                <label class="relative cursor-pointer">
                    <input type="radio" name="active_template" value="market_v2"
                           {{ ($portal->active_template ?? 'default') === 'market_v2' ? 'checked' : '' }} class="peer sr-only">
                    <div class="border-2 rounded-xl p-4 transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-gray-300">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-800">Market v2</span>
                                <span class="ml-2 text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">Premium</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500">Diseño tipo marketplace moderno.</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Hero Section -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Seccion Hero</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titulo Hero</label>
                    <input type="text" name="hero_title" value="{{ old('hero_title', $portal->hero_title) }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtitulo Hero</label>
                    <input type="text" name="hero_subtitle" value="{{ old('hero_subtitle', $portal->hero_subtitle) }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Texto del Boton CTA</label>
                    <input type="text" name="hero_cta_text" value="{{ old('hero_cta_text', $portal->hero_cta_text ?? 'Explorar tiendas') }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
        </div>

        <!-- Colores -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Colores del Tema</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color Primario</label>
                    <div class="flex gap-2">
                        <input type="color" name="primary_color" value="{{ old('primary_color', $portal->primary_color ?? '#16a34a') }}"
                               class="h-10 w-20 border rounded cursor-pointer">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color Secundario</label>
                    <div class="flex gap-2">
                        <input type="color" name="secondary_color" value="{{ old('secondary_color', $portal->secondary_color ?? '#6b7280') }}"
                               class="h-10 w-20 border rounded cursor-pointer">
                    </div>
                </div>
            </div>
        </div>

        <!-- Flyer Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Flyer / Productos Destacados</h2>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="flyer_enabled" value="1" {{ ($portal->flyer_enabled ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 text-primary-600 rounded">
                    <span class="text-sm text-gray-700">Activado</span>
                </label>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titulo del Flyer</label>
                    <input type="text" name="flyer_title" value="{{ old('flyer_title', $portal->flyer_title ?? 'Productos destacados') }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtitulo</label>
                    <input type="text" name="flyer_subtitle" value="{{ old('flyer_subtitle', $portal->flyer_subtitle) }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max productos por tienda</label>
                    <input type="number" name="flyer_max_per_store" value="{{ old('flyer_max_per_store', $portal->flyer_max_per_store ?? 5) }}"
                           min="1" max="10"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color de fondo</label>
                    <input type="color" name="flyer_accent_color" value="{{ old('flyer_accent_color', $portal->flyer_accent_color ?? '#16a34a') }}"
                           class="h-10 w-20 border rounded cursor-pointer">
                </div>
            </div>
        </div>

        <!-- Developer Info -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Desarrollado Por (Footer)</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text" name="developer_name" value="{{ old('developer_name', $portal->developer_name) }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                    <input type="url" name="developer_url" value="{{ old('developer_url', $portal->developer_url) }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="developer_email" value="{{ old('developer_email', $portal->developer_email) }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                    <input type="text" name="developer_whatsapp" value="{{ old('developer_whatsapp', $portal->developer_whatsapp) }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
        </div>

        <!-- Configuración General -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Configuracion General</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Redireccion del Home</label>
                    <input type="text" name="home_redirect_path" value="{{ old('home_redirect_path', $portal->home_redirect_path ?? 'portal') }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Promos por Tienda</label>
                    <input type="number" name="promos_per_store" value="{{ old('promos_per_store', $portal->promos_per_store ?? 1) }}"
                           min="1" max="10"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="md:col-span-2 space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="show_prices_in_portal" value="1" {{ ($portal->show_prices_in_portal ?? true) ? 'checked' : '' }}
                               class="w-4 h-4 text-primary-600 rounded">
                        <span class="text-sm text-gray-700">Mostrar precios en el portal</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="ai_assistant_enabled" value="1" {{ ($portal->ai_assistant_enabled ?? true) ? 'checked' : '' }}
                               class="w-4 h-4 text-primary-600 rounded">
                        <span class="text-sm text-gray-700">Activar Asistente IA</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Empresas Asignadas -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Empresas Asignadas ({{ $portal->empresas->count() }})</h2>
            @if($portal->empresas->count() > 0)
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-2 max-h-48 overflow-y-auto">
                @foreach($portal->empresas as $empresa)
                <div class="flex items-center gap-2 p-2 bg-gray-50 rounded">
                    <span class="text-sm text-gray-700">{{ $empresa->nombre }}</span>
                </div>
                @endforeach
            </div>
            <p class="text-xs text-gray-500 mt-2">Para cambiar las empresas asignadas, edita cada empresa individualmente.</p>
            @else
            <p class="text-sm text-gray-500">No hay empresas asignadas a este portal.</p>
            @endif
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.portales.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>
@endsection

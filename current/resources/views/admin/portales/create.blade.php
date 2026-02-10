@extends('layouts.admin', ['title' => 'Crear Portal', 'header' => 'Nuevo Portal'])

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.portales.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

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
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug (URL)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500"
                           placeholder="mi-portal">
                    <p class="text-xs text-gray-500 mt-1">Se genera automaticamente si se deja vacio</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dominio</label>
                    <input type="text" name="dominio" value="{{ old('dominio') }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500"
                           placeholder="miportal.com">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tagline</label>
                    <input type="text" name="tagline" value="{{ old('tagline') }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripcion</label>
                    <textarea name="descripcion" rows="2"
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">{{ old('descripcion') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                    <input type="file" name="logo" accept="image/*"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="flex items-center">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="activo" value="1" checked
                               class="w-4 h-4 text-primary-600 rounded">
                        <span class="text-sm text-gray-700">Portal Activo</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Template -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Template del Portal</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <label class="relative cursor-pointer">
                    <input type="radio" name="active_template" value="classic" checked class="peer sr-only">
                    <div class="border-2 rounded-xl p-4 transition-all peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-gray-300">
                        <div class="w-full h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-lg mb-3 flex items-end p-2">
                            <div class="flex gap-1"><div class="w-3 h-3 bg-white/80 rounded"></div><div class="w-3 h-3 bg-white/60 rounded"></div><div class="w-3 h-3 bg-white/40 rounded"></div></div>
                        </div>
                        <span class="font-semibold text-gray-800 block">Classic</span>
                        <p class="text-xs text-gray-500">Hero + Grid tradicional</p>
                    </div>
                </label>
                <label class="relative cursor-pointer">
                    <input type="radio" name="active_template" value="modern" class="peer sr-only">
                    <div class="border-2 rounded-xl p-4 transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 hover:border-gray-300">
                        <div class="w-full h-16 bg-gradient-to-br from-gray-800 to-gray-900 rounded-lg mb-3 flex items-end p-2">
                            <div class="flex gap-1"><div class="w-3 h-3 bg-emerald-400 rounded"></div><div class="w-3 h-3 bg-emerald-300 rounded"></div><div class="w-3 h-3 bg-emerald-200 rounded"></div></div>
                        </div>
                        <span class="font-semibold text-gray-800 block">Modern</span>
                        <p class="text-xs text-gray-500">Sidebar + Dark hero</p>
                    </div>
                </label>
                <label class="relative cursor-pointer">
                    <input type="radio" name="active_template" value="minimal" class="peer sr-only">
                    <div class="border-2 rounded-xl p-4 transition-all peer-checked:border-gray-400 peer-checked:bg-gray-50 hover:border-gray-300">
                        <div class="w-full h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg mb-3 flex items-center justify-center">
                            <div class="flex gap-1"><div class="w-2 h-6 bg-gray-300 rounded"></div><div class="w-2 h-6 bg-gray-400 rounded"></div><div class="w-2 h-6 bg-gray-300 rounded"></div></div>
                        </div>
                        <span class="font-semibold text-gray-800 block">Minimal</span>
                        <p class="text-xs text-gray-500">Ultra limpio, elegante</p>
                    </div>
                </label>
                <label class="relative cursor-pointer">
                    <input type="radio" name="active_template" value="bold" class="peer sr-only">
                    <div class="border-2 rounded-xl p-4 transition-all peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:border-gray-300">
                        <div class="w-full h-16 bg-gradient-to-br from-orange-400 to-rose-500 rounded-lg mb-3 flex items-end p-2">
                            <div class="flex gap-1"><div class="w-3 h-3 bg-white/80 rounded-full"></div><div class="w-3 h-3 bg-white/60 rounded-full"></div><div class="w-3 h-3 bg-white/40 rounded-full"></div></div>
                        </div>
                        <span class="font-semibold text-gray-800 block">Bold</span>
                        <p class="text-xs text-gray-500">Vibrante, app mobile</p>
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
                    <input type="text" name="hero_title" value="{{ old('hero_title') }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtitulo Hero</label>
                    <input type="text" name="hero_subtitle" value="{{ old('hero_subtitle') }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Texto del Boton CTA</label>
                    <input type="text" name="hero_cta_text" value="{{ old('hero_cta_text', 'Explorar tiendas') }}"
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
                        <input type="color" name="primary_color" value="{{ old('primary_color', '#16a34a') }}"
                               class="h-10 w-20 border rounded cursor-pointer">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color Secundario</label>
                    <div class="flex gap-2">
                        <input type="color" name="secondary_color" value="{{ old('secondary_color', '#6b7280') }}"
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
                    <input type="checkbox" name="flyer_enabled" value="1" checked
                           class="w-4 h-4 text-primary-600 rounded">
                    <span class="text-sm text-gray-700">Activado</span>
                </label>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titulo del Flyer</label>
                    <input type="text" name="flyer_title" value="{{ old('flyer_title', 'Productos destacados') }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtitulo</label>
                    <input type="text" name="flyer_subtitle" value="{{ old('flyer_subtitle') }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max productos por tienda</label>
                    <input type="number" name="flyer_max_per_store" value="{{ old('flyer_max_per_store', 5) }}"
                           min="1" max="10"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color de fondo</label>
                    <input type="color" name="flyer_accent_color" value="{{ old('flyer_accent_color', '#16a34a') }}"
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
                    <input type="text" name="developer_name" value="{{ old('developer_name') }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                    <input type="url" name="developer_url" value="{{ old('developer_url') }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="developer_email" value="{{ old('developer_email') }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                    <input type="text" name="developer_whatsapp" value="{{ old('developer_whatsapp') }}"
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
                    <input type="text" name="home_redirect_path" value="{{ old('home_redirect_path', 'portal') }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Promos por Tienda</label>
                    <input type="number" name="promos_per_store" value="{{ old('promos_per_store', 1) }}"
                           min="1" max="10"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="md:col-span-2 space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="show_prices_in_portal" value="1" checked
                               class="w-4 h-4 text-primary-600 rounded">
                        <span class="text-sm text-gray-700">Mostrar precios en el portal</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="ai_assistant_enabled" value="1" checked
                               class="w-4 h-4 text-primary-600 rounded">
                        <span class="text-sm text-gray-700">Activar Asistente IA</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.portales.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium">
                Crear Portal
            </button>
        </div>
    </form>
</div>
@endsection

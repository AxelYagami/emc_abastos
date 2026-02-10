@extends('layouts.admin', ['title' => 'Nueva Empresa', 'header' => 'Nueva Empresa'])

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('admin.empresas.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a Empresas
        </a>
    </div>

    <form method="POST" action="{{ route('admin.empresas.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Tabs -->
        <div x-data="{ tab: 'branding' }" class="bg-white rounded-lg shadow">
            <div class="border-b">
                <nav class="flex -mb-px">
                    <button type="button" @click="tab = 'branding'"
                            :class="tab === 'branding' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="py-4 px-6 border-b-2 font-medium text-sm">
                        Branding
                    </button>
                    <button type="button" @click="tab = 'pagos'"
                            :class="tab === 'pagos' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="py-4 px-6 border-b-2 font-medium text-sm">
                        Pagos
                    </button>
                    <button type="button" @click="tab = 'catalogo'"
                            :class="tab === 'catalogo' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="py-4 px-6 border-b-2 font-medium text-sm">
                        Catalogo
                    </button>
                </nav>
            </div>

            <!-- Branding Tab -->
            <div x-show="tab === 'branding'" class="p-6 space-y-4">
                {{-- Portal selector --}}
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <label class="block text-sm font-medium text-blue-800 mb-1">Portal asignado</label>
                    <select name="portal_id" class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">-- Sin portal (global) --</option>
                        @foreach(\App\Models\Portal::where('activo', true)->orderBy('nombre')->get() as $portal)
                        <option value="{{ $portal->id }}" {{ old('portal_id', session('current_portal_id')) == $portal->id ? 'selected' : '' }}>{{ $portal->nombre }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-blue-600 mt-1">La empresa aparecera solo en el portal seleccionado</p>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre interno *</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                        @error('nombre')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug (URL)</label>
                        <input type="text" name="slug" value="{{ old('slug') }}"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500"
                               placeholder="auto-generado si vacio">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre comercial (display)</label>
                        <input type="text" name="brand_nombre_publico" value="{{ old('brand_nombre_publico') }}"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del sistema (app_name)</label>
                        <input type="text" name="app_name" value="{{ old('app_name') }}"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500"
                               placeholder="Ej: Mi Tienda Online">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                    <input type="file" name="logo" accept="image/*"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color primario</label>
                        <input type="color" name="primary_color" value="{{ old('primary_color', '#16a34a') }}"
                               class="w-full h-10 border rounded-lg cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color secundario</label>
                        <input type="color" name="secondary_color" value="{{ old('secondary_color', '#6b7280') }}"
                               class="w-full h-10 border rounded-lg cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color acento</label>
                        <input type="color" name="accent_color" value="{{ old('accent_color', '#3b82f6') }}"
                               class="w-full h-10 border rounded-lg cursor-pointer">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tema</label>
                    <select name="theme_id" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Sin tema</option>
                        @foreach($themes as $theme)
                            <option value="{{ $theme->id }}">{{ $theme->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Storefront Template Selector --}}
                <div class="mt-6 pt-6 border-t">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Plantilla de Tienda (Storefront)</label>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        {{-- Classic Template --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="storefront_template" value="classic" class="sr-only peer" checked>
                            <div class="border-2 rounded-xl overflow-hidden peer-checked:border-primary-500 peer-checked:ring-2 peer-checked:ring-primary-200 hover:border-gray-300 transition-all">
                                <div class="h-24 bg-gradient-to-br from-emerald-500 to-emerald-600 relative overflow-hidden">
                                    <div class="absolute inset-3 bg-white/20 rounded"></div>
                                    <div class="absolute bottom-2 left-3 right-3 flex gap-1">
                                        <div class="w-6 h-6 bg-white/30 rounded"></div>
                                        <div class="w-6 h-6 bg-white/30 rounded"></div>
                                        <div class="w-6 h-6 bg-white/30 rounded"></div>
                                    </div>
                                </div>
                                <div class="p-3">
                                    <h4 class="font-semibold text-gray-800 text-sm">Classic</h4>
                                    <p class="text-xs text-gray-500">Hero + Grid tradicional</p>
                                </div>
                            </div>
                        </label>

                        {{-- Modern Template --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="storefront_template" value="modern" class="sr-only peer">
                            <div class="border-2 rounded-xl overflow-hidden peer-checked:border-primary-500 peer-checked:ring-2 peer-checked:ring-primary-200 hover:border-gray-300 transition-all">
                                <div class="h-24 bg-gradient-to-br from-slate-800 to-slate-900 relative overflow-hidden">
                                    <div class="absolute top-2 left-2 w-12 h-10 bg-white/10 rounded"></div>
                                    <div class="absolute top-2 right-2 w-10 h-10 bg-gradient-to-br from-emerald-400/30 to-blue-400/30 rounded-lg"></div>
                                </div>
                                <div class="p-3">
                                    <h4 class="font-semibold text-gray-800 text-sm">Modern</h4>
                                    <p class="text-xs text-gray-500">Sidebar + Dark hero</p>
                                </div>
                            </div>
                        </label>

                        {{-- Minimal Template --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="storefront_template" value="minimal" class="sr-only peer">
                            <div class="border-2 rounded-xl overflow-hidden peer-checked:border-primary-500 peer-checked:ring-2 peer-checked:ring-primary-200 hover:border-gray-300 transition-all">
                                <div class="h-24 bg-white relative overflow-hidden">
                                    <div class="absolute top-4 left-1/2 -translate-x-1/2 w-20 h-3 bg-slate-200 rounded-full"></div>
                                    <div class="absolute bottom-2 left-3 right-3 flex gap-2 justify-center">
                                        <div class="w-8 h-8 bg-slate-100 rounded"></div>
                                        <div class="w-8 h-8 bg-slate-100 rounded"></div>
                                    </div>
                                </div>
                                <div class="p-3">
                                    <h4 class="font-semibold text-gray-800 text-sm">Minimal</h4>
                                    <p class="text-xs text-gray-500">Ultra limpio</p>
                                </div>
                            </div>
                        </label>

                        {{-- Bold Template --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="storefront_template" value="bold" class="sr-only peer">
                            <div class="border-2 rounded-xl overflow-hidden peer-checked:border-primary-500 peer-checked:ring-2 peer-checked:ring-primary-200 hover:border-gray-300 transition-all">
                                <div class="h-24 bg-gradient-to-br from-orange-400 to-pink-500 relative overflow-hidden">
                                    <div class="absolute -top-4 -right-4 w-16 h-16 bg-white/20 rounded-full blur-xl"></div>
                                    <div class="absolute bottom-2 left-3 flex gap-2">
                                        <div class="w-10 h-10 bg-white/40 rounded-xl"></div>
                                    </div>
                                </div>
                                <div class="p-3">
                                    <h4 class="font-semibold text-gray-800 text-sm">Bold</h4>
                                    <p class="text-xs text-gray-500">Vibrante, mobile</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="activa" value="1" checked id="activa"
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="activa" class="ml-2 text-sm text-gray-700">Empresa activa</label>
                </div>
            </div>

            <!-- Pagos Tab -->
            <div x-show="tab === 'pagos'" x-cloak class="p-6 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm text-blue-800 font-medium">Credenciales de MercadoPago</p>
                            <p class="text-xs text-blue-600 mt-1">Obtén las credenciales desde el panel de desarrolladores de MercadoPago</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Access Token</label>
                    <input type="password" name="mp_access_token" value="{{ old('mp_access_token') }}"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 font-mono text-sm"
                           placeholder="APP_USR-...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Public Key</label>
                    <input type="text" name="mp_public_key" value="{{ old('mp_public_key') }}"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 font-mono text-sm"
                           placeholder="APP_USR-...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Webhook Secret (opcional)</label>
                    <input type="text" name="mp_webhook_secret" value="{{ old('mp_webhook_secret') }}"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 font-mono text-sm">
                </div>
            </div>

            <!-- Catalogo Tab -->
            <div x-show="tab === 'catalogo'" x-cloak class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL imagen default para productos</label>
                    <input type="url" name="default_product_image_url" value="{{ old('default_product_image_url') }}"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500"
                           placeholder="https://...">
                    <p class="text-xs text-gray-500 mt-1">Se usara cuando un producto no tenga imagen</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.empresas.index') }}"
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                Crear Empresa
            </button>
        </div>
    </form>
</div>
@endsection

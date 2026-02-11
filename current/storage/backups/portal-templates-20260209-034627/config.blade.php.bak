@extends('layouts.admin', ['title' => 'Portal Central', 'header' => 'Configuracion del Portal'])

@section('content')
<div class="max-w-4xl mx-auto">
    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('admin.portal.config.update') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Template Selector -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Template del Portal</h2>
            <p class="text-sm text-gray-500 mb-4">Selecciona el diseño visual del portal publico. Ambos templates usan la misma configuracion.</p>
            <div class="grid sm:grid-cols-2 gap-4">
                <label class="relative cursor-pointer">
                    <input type="radio" name="active_template" value="default"
                           {{ ($config['active_template'] ?? 'default') === 'default' ? 'checked' : '' }}
                           class="peer sr-only">
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
                        <p class="text-sm text-gray-500">Hero con gradiente, secciones verticales, tiendas en grid cuadrado. Limpio y directo.</p>
                    </div>
                </label>
                <label class="relative cursor-pointer">
                    <input type="radio" name="active_template" value="market_v2"
                           {{ ($config['active_template'] ?? 'default') === 'market_v2' ? 'checked' : '' }}
                           class="peer sr-only">
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
                        <p class="text-sm text-gray-500">Diseño tipo marketplace moderno. Hero full-width con imagen, cards glassmorphism, layout magazine.</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- General -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Informacion General</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Portal</label>
                    <input type="text" name="portal_name" value="{{ $config['portal_name'] ?? '' }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tagline</label>
                    <input type="text" name="portal_tagline" value="{{ $config['portal_tagline'] ?? '' }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripcion</label>
                    <textarea name="portal_description" rows="3"
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">{{ $config['portal_description'] ?? '' }}</textarea>
                </div>
            </div>
        </div>

        <!-- Hero Section -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Seccion Hero</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titulo Hero</label>
                    <input type="text" name="hero_title" value="{{ $config['hero_title'] ?? '' }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtitulo Hero</label>
                    <input type="text" name="hero_subtitle" value="{{ $config['hero_subtitle'] ?? '' }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Texto del Boton CTA</label>
                    <input type="text" name="hero_cta_text" value="{{ $config['hero_cta_text'] ?? 'Explorar tiendas' }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
        </div>

        <!-- Flyer Section -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Flyer / Banner de Productos Destacados</h2>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="flyer_enabled" value="1"
                           {{ ($config['flyer_enabled'] ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 text-primary-600 rounded">
                    <span class="text-sm text-gray-700">Activado</span>
                </label>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titulo del Flyer</label>
                    <input type="text" name="flyer_title" value="{{ $config['flyer_title'] ?? 'Productos destacados' }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtitulo</label>
                    <input type="text" name="flyer_subtitle" value="{{ $config['flyer_subtitle'] ?? '' }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Maximo productos por tienda</label>
                    <input type="number" name="flyer_max_per_store" value="{{ $config['flyer_max_per_store'] ?? 5 }}"
                           min="1" max="10"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                    <p class="text-xs text-gray-500 mt-1">Cada tienda puede marcar hasta este numero de productos como destacados</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color de fondo (opcional)</label>
                    <div class="flex gap-2 items-center">
                        <input type="color" name="flyer_accent_color" value="{{ $config['flyer_accent_color'] ?? '#16a34a' }}"
                               class="h-10 w-20 border rounded cursor-pointer" id="flyer_color_picker">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="flyer_no_color" {{ empty($config['flyer_accent_color']) ? 'checked' : '' }}
                                   class="w-4 h-4 text-primary-600 rounded"
                                   onchange="document.getElementById('flyer_color_picker').disabled = this.checked; if(this.checked) document.getElementById('flyer_color_picker').value = '';">
                            <span class="text-sm text-gray-600">Sin color (fondo neutro)</span>
                        </label>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Productos Destacados</label>
                    <p class="text-xs text-gray-500 mb-3">Selecciona los productos que apareceran en el flyer del portal. Maximo {{ $config['flyer_max_per_store'] ?? 5 }} por tienda.</p>
                    
                    @php
                        $productosPorEmpresa = $productos->groupBy('empresa_id');
                        $selectedIds = $flyerProductIds ?? [];
                    @endphp
                    
                    <div class="border rounded-lg divide-y max-h-96 overflow-y-auto" x-data="{ 
                        selected: {{ json_encode($selectedIds) }},
                        maxPerStore: {{ $config['flyer_max_per_store'] ?? 5 }},
                        countByStore: {},
                        init() {
                            this.updateCounts();
                        },
                        updateCounts() {
                            this.countByStore = {};
                            document.querySelectorAll('[data-empresa-id]').forEach(cb => {
                                if (cb.checked) {
                                    const empId = cb.dataset.empresaId;
                                    this.countByStore[empId] = (this.countByStore[empId] || 0) + 1;
                                }
                            });
                        },
                        canSelect(empresaId, isChecked) {
                            if (!isChecked) return true;
                            const current = this.countByStore[empresaId] || 0;
                            return current < this.maxPerStore;
                        }
                    }">
                        @foreach($empresas as $empresa)
                            @php $empresaProductos = $productosPorEmpresa->get($empresa->id, collect()); @endphp
                            @if($empresaProductos->count() > 0)
                            <div class="p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-medium text-gray-800">{{ $empresa->nombre }}</span>
                                    <span class="text-xs px-2 py-1 rounded-full" 
                                          :class="(countByStore[{{ $empresa->id }}] || 0) >= maxPerStore ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600'"
                                          x-text="(countByStore[{{ $empresa->id }}] || 0) + '/' + maxPerStore + ' seleccionados'">
                                    </span>
                                </div>
                                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                    @foreach($empresaProductos as $p)
                                    <label class="flex items-center gap-2 p-2 hover:bg-gray-50 rounded cursor-pointer text-sm">
                                        <input type="checkbox" 
                                               name="flyer_product_ids[]" 
                                               value="{{ $p->id }}"
                                               data-empresa-id="{{ $empresa->id }}"
                                               {{ in_array($p->id, $selectedIds) ? 'checked' : '' }}
                                               @change="
                                                   if ($event.target.checked && !canSelect({{ $empresa->id }}, true)) {
                                                       $event.target.checked = false;
                                                       alert('Maximo ' + maxPerStore + ' productos por tienda');
                                                       return;
                                                   }
                                                   updateCounts();
                                               "
                                               class="w-4 h-4 text-primary-600 rounded">
                                        <span class="truncate text-gray-700">{{ $p->nombre }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured Stores -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Tiendas Destacadas</h2>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Seleccionar tiendas destacadas</label>
                <p class="text-xs text-gray-500 mb-2">Estas tiendas apareceran primero en el directorio</p>
                <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-2 max-h-48 overflow-y-auto border rounded-lg p-3">
                    @foreach($empresas as $e)
                        <label class="flex items-center gap-2 p-2 hover:bg-gray-50 rounded cursor-pointer">
                            <input type="checkbox" name="featured_store_ids[]" value="{{ $e->id }}"
                                   {{ in_array($e->id, $featuredStoreIds ?? []) ? 'checked' : '' }}
                                   class="w-4 h-4 text-primary-600 rounded">
                            <span class="text-sm text-gray-700 truncate">{{ $e->nombre }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- AI Assistant -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Asistente IA</h2>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="ai_assistant_enabled" value="1"
                           {{ ($config['ai_assistant_enabled'] ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 text-primary-600 rounded">
                    <span class="text-sm text-gray-700">Activado</span>
                </label>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titulo del Asistente</label>
                    <input type="text" name="ai_assistant_title" value="{{ $config['ai_assistant_title'] ?? 'Asistente IA' }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mensaje de bienvenida</label>
                    <textarea name="ai_assistant_welcome" rows="2"
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">{{ $config['ai_assistant_welcome'] ?? '' }}</textarea>
                </div>
            </div>
        </div>

        <!-- Developer Info -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Desarrollado Por (Footer)</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text" name="developer_name" value="{{ $config['developer_name'] ?? 'iaDoS.mx' }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                    <input type="url" name="developer_url" value="{{ $config['developer_url'] ?? 'https://iados.mx' }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="developer_email" value="{{ $config['developer_email'] ?? 'contacto@iados.mx' }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                    <input type="text" name="developer_whatsapp" value="{{ $config['developer_whatsapp'] ?? '8318989580' }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
        </div>

        <!-- Home Redirect -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Redireccion del Home</h2>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ruta al entrar a la raiz del sitio</label>
                <div class="flex items-center gap-2">
                    <span class="text-gray-500">/</span>
                    <input type="text" name="home_redirect_path" value="{{ $config['home_redirect_path'] ?? 'portal' }}"
                           class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500"
                           placeholder="portal">
                </div>
                <p class="text-xs text-gray-500 mt-1">Cuando alguien visite la raiz del sitio ({{ config('app.url') }}) sera redirigido a esta ruta. Ejemplo: <strong>portal</strong>, <strong>tienda</strong>, <strong>inicio</strong></p>
            </div>
        </div>

        <!-- Theme Colors -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Colores del Tema</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color Primario</label>
                    <div class="flex gap-2">
                        <input type="color" name="primary_color" value="{{ $config['primary_color'] ?? '#16a34a' }}"
                               class="h-10 w-20 border rounded cursor-pointer">
                        <input type="text" value="{{ $config['primary_color'] ?? '#16a34a' }}"
                               class="flex-1 px-4 py-2 border rounded-lg bg-gray-50" readonly>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color Secundario</label>
                    <div class="flex gap-2">
                        <input type="color" name="secondary_color" value="{{ $config['secondary_color'] ?? '#6b7280' }}"
                               class="h-10 w-20 border rounded cursor-pointer">
                        <input type="text" value="{{ $config['secondary_color'] ?? '#6b7280' }}"
                               class="flex-1 px-4 py-2 border rounded-lg bg-gray-50" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Configuracion General</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dominio Externo (opcional)</label>
                    <input type="text" name="fallback_domain" value="{{ $config['fallback_domain'] ?? '' }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500"
                           placeholder="Dejar vacio para usar {{ parse_url(config('app.url'), PHP_URL_HOST) }}">
                    <p class="text-xs text-gray-500 mt-1">Solo si usas un dominio externo. Por defecto usa APP_URL ({{ config('app.url') }})</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Promos por Tienda en Portal</label>
                    <input type="number" name="promos_per_store" value="{{ $config['promos_per_store'] ?? 1 }}"
                           min="1" max="10"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="md:col-span-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="show_prices_in_portal" value="1"
                               {{ ($config['show_prices_in_portal'] ?? true) ? 'checked' : '' }}
                               class="w-4 h-4 text-primary-600 rounded">
                        <span class="text-sm text-gray-700">Mostrar precios en el portal</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium">
                Guardar Configuracion
            </button>
        </div>
    </form>
</div>
@endsection

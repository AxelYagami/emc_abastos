{{-- 
    BOLD TEMPLATE - EMC Abastos Storefront
    Vibrant colors, large cards with gradients, mobile-app style
    Big typography, rounded corners, playful animations
    Best for: Food delivery, trendy stores, youth-focused brands
--}}

<div class="min-h-screen" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
    {{-- Bold Hero with Gradient --}}
    <section class="relative overflow-hidden">
        {{-- Animated Background Shapes --}}
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full opacity-60 blur-3xl animate-pulse" 
                 style="background: var(--brand-primary);"></div>
            <div class="absolute -bottom-20 -left-20 w-96 h-96 rounded-full opacity-40 blur-3xl animate-pulse" 
                 style="background: var(--brand-accent); animation-delay: 1s;"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 py-12 lg:py-20">
            <div class="text-center">
                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/80 backdrop-blur-sm rounded-full shadow-lg mb-6">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background: var(--brand-primary);"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3" style="background: var(--brand-primary);"></span>
                    </span>
                    <span class="text-sm font-semibold text-slate-700">Abierto ahora</span>
                </div>
                
                <h1 class="text-5xl lg:text-7xl font-black tracking-tight text-slate-900 mb-6">
                    {{ $currentStore->brand_nombre_publico ?? $currentStore->nombre ?? 'Tienda' }}
                </h1>
                
                <p class="text-xl text-slate-600 max-w-lg mx-auto mb-8">
                    {{ $currentStore->descripcion ?? 'Los mejores productos al mejor precio' }}
                </p>
                
                {{-- Search Bar (bold style) --}}
                <form method="GET" class="max-w-xl mx-auto">
                    <div class="relative">
                        <input type="text"
                               name="q"
                               value="{{ $q }}"
                               placeholder="Que estas buscando hoy?"
                               class="w-full px-6 py-5 text-lg bg-white border-0 rounded-2xl shadow-xl focus:ring-4 transition-all"
                               style="--tw-ring-color: color-mix(in srgb, var(--brand-primary) 30%, transparent);">
                        <button type="submit" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 p-3 text-white rounded-xl shadow-lg transition-transform hover:scale-110"
                                style="background: var(--brand-primary);">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- Category Chips (bold colorful) --}}
    @if($categorias->count() > 0)
    <section class="max-w-7xl mx-auto px-4 py-6">
        <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
            <a href="{{ url()->current() }}" 
               class="flex-shrink-0 px-6 py-3 rounded-2xl text-sm font-bold shadow-md transition-all transform hover:scale-105 {{ !$categoriaId ? 'text-white' : 'bg-white text-slate-700 hover:shadow-lg' }}"
               style="{{ !$categoriaId ? 'background: linear-gradient(135deg, var(--brand-primary), var(--brand-accent));' : '' }}">
                🔥 Todo
            </a>
            @foreach($categorias as $index => $cat)
            @php
                $emojis = ['🍎', '🥬', '🥩', '🧀', '🍞', '🥛', '🍳', '🥫'];
                $emoji = $emojis[$index % count($emojis)];
            @endphp
            <a href="{{ url()->current() }}?categoria_id={{ $cat->id }}" 
               class="flex-shrink-0 px-6 py-3 rounded-2xl text-sm font-bold shadow-md transition-all transform hover:scale-105 {{ $categoriaId == $cat->id ? 'text-white' : 'bg-white text-slate-700 hover:shadow-lg' }}"
               style="{{ $categoriaId == $cat->id ? 'background: linear-gradient(135deg, var(--brand-primary), var(--brand-accent));' : '' }}">
                {{ $emoji }} {{ $cat->nombre }}
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Products Section --}}
    <section id="productos" class="max-w-7xl mx-auto px-4 py-8">
        {{-- Section Header --}}
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl lg:text-3xl font-black text-slate-900">
                @if($q)
                    🔍 Resultados
                @elseif($categoriaId)
                    {{ $categorias->firstWhere('id', $categoriaId)?->nombre ?? 'Productos' }}
                @else
                    ⭐ Para ti
                @endif
            </h2>
            <span class="px-4 py-2 bg-white rounded-xl text-sm font-bold text-slate-600 shadow">
                {{ $productos->total() }} items
            </span>
        </div>

        {{-- Products Grid (bold cards) --}}
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 lg:gap-6">
            @forelse($productos as $index => $producto)
                <div class="group bg-white rounded-3xl shadow-lg overflow-hidden transition-all transform hover:scale-[1.02] hover:shadow-2xl">
                    {{-- Image --}}
                    <div class="relative aspect-square overflow-hidden">
                        <img src="{{ $producto->display_image ?? $producto->imagen_url ?? '/images/producto-default.svg' }}" 
                             alt="{{ $producto->nombre }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                             loading="{{ $index > 7 ? 'lazy' : 'eager' }}">
                        
                        {{-- Price Badge --}}
                        <div class="absolute top-3 right-3 px-3 py-1.5 bg-white/90 backdrop-blur-sm rounded-full shadow-lg">
                            <span class="text-lg font-black" style="color: var(--brand-primary);">
                                ${{ number_format($producto->precio, 0) }}
                            </span>
                        </div>
                        
                        {{-- Category Badge --}}
                        @if($producto->categoria)
                        <div class="absolute top-3 left-3 px-3 py-1.5 rounded-full text-xs font-bold text-white shadow-lg"
                             style="background: linear-gradient(135deg, var(--brand-primary), var(--brand-accent));">
                            {{ Str::limit($producto->categoria->nombre, 10) }}
                        </div>
                        @endif

                        {{-- Quick Add Overlay --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-6">
                            <button type="button"
                                    onclick="storefrontApp().addToCart({{ $producto->id }}, 1, this)"
                                    class="px-6 py-3 bg-white text-slate-900 rounded-xl font-bold shadow-xl transform translate-y-4 group-hover:translate-y-0 transition-all flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Agregar
                            </button>
                        </div>
                    </div>
                    
                    {{-- Product Info --}}
                    <div class="p-4">
                        <h3 class="font-bold text-slate-800 line-clamp-2 leading-tight">
                            {{ $producto->nombre }}
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ $producto->unidad ?? 'Por unidad' }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20">
                    <div class="text-6xl mb-4">😕</div>
                    <h3 class="text-2xl font-bold text-slate-700 mb-2">No hay productos</h3>
                    <p class="text-slate-500">Intenta con otra busqueda</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($productos->hasPages())
        <div class="mt-12 flex justify-center">
            {{ $productos->links() }}
        </div>
        @endif
    </section>

    {{-- Bold Stats Section --}}
    <section class="max-w-7xl mx-auto px-4 py-12">
        <div class="relative overflow-hidden rounded-3xl p-8 lg:p-12 text-white"
             style="background: linear-gradient(135deg, var(--brand-primary), color-mix(in srgb, var(--brand-primary) 60%, var(--brand-accent)));">
            {{-- Background Pattern --}}
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <defs>
                        <pattern id="dots" width="10" height="10" patternUnits="userSpaceOnUse">
                            <circle cx="2" cy="2" r="1.5" fill="white"/>
                        </pattern>
                    </defs>
                    <rect width="100" height="100" fill="url(#dots)" />
                </svg>
            </div>
            
            <div class="relative grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-4xl lg:text-5xl font-black">{{ $productos->total() }}+</div>
                    <div class="text-sm opacity-80 mt-1">Productos</div>
                </div>
                <div>
                    <div class="text-4xl lg:text-5xl font-black">24h</div>
                    <div class="text-sm opacity-80 mt-1">Entrega</div>
                </div>
                <div>
                    <div class="text-4xl lg:text-5xl font-black">100%</div>
                    <div class="text-sm opacity-80 mt-1">Fresco</div>
                </div>
                <div>
                    <div class="text-4xl lg:text-5xl font-black">5★</div>
                    <div class="text-sm opacity-80 mt-1">Rating</div>
                </div>
            </div>
        </div>
    </section>

    {{-- WhatsApp Float Button --}}
    @if($currentStore->getSetting('whatsapp'))
    <a href="https://wa.me/52{{ preg_replace('/\D/', '', $currentStore->getSetting('whatsapp')) }}"
       target="_blank"
       class="fixed bottom-24 right-6 w-14 h-14 bg-green-500 text-white rounded-full shadow-2xl flex items-center justify-center transition-transform hover:scale-110 z-40">
        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
    </a>
    @endif
</div>

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

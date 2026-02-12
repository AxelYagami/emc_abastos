{{-- 
    MODERN TEMPLATE - EMC Abastos Storefront
    Magazine-style layout with sidebar categories, featured products, glassmorphism
    Best for: Tech-savvy stores, premium products, modern marketplaces
--}}

<div class="min-h-screen">
    {{-- Hero Section with Glassmorphism --}}
    @if($flyers && $flyers->count() > 0)
    <section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        {{-- Animated Background --}}
        <div class="absolute inset-0">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-[var(--brand-primary)] opacity-20 rounded-full blur-[120px] animate-pulse"></div>
            <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-[var(--brand-accent)] opacity-15 rounded-full blur-[100px] animate-pulse" style="animation-delay: 1s;"></div>
        </div>
        
        {{-- Grid Pattern --}}
        <div class="absolute inset-0 opacity-5" style="background-image: linear-gradient(rgba(255,255,255,.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.1) 1px, transparent 1px); background-size: 40px 40px;"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                {{-- Left Content --}}
                <div class="text-white space-y-6">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-md rounded-full border border-white/20">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                        <span class="text-sm font-medium">Tienda en linea</span>
                    </div>
                    <h1 class="text-4xl lg:text-6xl font-bold leading-tight">
                        {{ $flyers->first()->titulo ?? 'Descubre productos increibles' }}
                    </h1>
                    <p class="text-lg text-slate-300 max-w-md">
                        {{ $flyers->first()->subtitulo ?? 'Los mejores productos seleccionados para ti, directo del mercado a tu puerta.' }}
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="#productos" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl font-bold text-white transition-all hover:scale-105 shadow-lg"
                           style="background: linear-gradient(135deg, var(--brand-primary), color-mix(in srgb, var(--brand-primary) 80%, var(--brand-accent)));">
                            Ver productos
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Right - Featured Product Card --}}
                @if($flyers->first() && $flyers->first()->productos && $flyers->first()->productos->count() > 0)
                @php $featuredProduct = $flyers->first()->productos->first(); @endphp
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-[var(--brand-primary)] to-[var(--brand-accent)] rounded-3xl blur-2xl opacity-30 scale-95"></div>
                    <div class="relative bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 p-6 overflow-hidden">
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 bg-amber-400 text-amber-900 text-xs font-bold rounded-full">DESTACADO</span>
                        </div>
                        <img src="{{ $featuredProduct->display_image ?? $featuredProduct->imagen_url ?? '/images/producto-default.svg' }}" 
                             alt="{{ $featuredProduct->nombre }}"
                             class="w-full h-64 object-cover rounded-2xl mb-4">
                        <h3 class="text-xl font-bold text-white mb-2">{{ $featuredProduct->nombre }}</h3>
                        <p class="text-2xl font-bold" style="color: var(--brand-primary);">${{ number_format($featuredProduct->precio, 2) }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    {{-- Main Content with Sidebar --}}
    <section id="productos" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="flex flex-col lg:flex-row gap-8">
            
            {{-- Sidebar (Categories) --}}
            <aside class="lg:w-64 flex-shrink-0">
                <div class="lg:sticky lg:top-24 space-y-6">
                    {{-- Categories Card --}}
                    <div class="bg-white rounded-2xl shadow-premium p-6">
                        <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" style="color: var(--brand-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                            </svg>
                            Categorias
                        </h3>
                        <nav class="space-y-1">
                            <a href="{{ url()->current() }}" 
                               class="flex items-center justify-between px-3 py-2 rounded-lg transition-all {{ !$categoriaId ? 'bg-[var(--brand-primary)] text-white font-medium' : 'text-slate-600 hover:bg-slate-100' }}">
                                <span>Todas</span>
                                <span class="text-xs px-2 py-0.5 rounded-full {{ !$categoriaId ? 'bg-white/20' : 'bg-slate-200' }}">{{ $productos->total() }}</span>
                            </a>
                            @foreach($categorias as $cat)
                            <a href="{{ url()->current() }}?categoria_id={{ $cat->id }}" 
                               class="flex items-center justify-between px-3 py-2 rounded-lg transition-all {{ $categoriaId == $cat->id ? 'bg-[var(--brand-primary)] text-white font-medium' : 'text-slate-600 hover:bg-slate-100' }}">
                                <span>{{ $cat->nombre }}</span>
                            </a>
                            @endforeach
                        </nav>
                    </div>

                    {{-- Quick Stats --}}
                    <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 text-white">
                        <h3 class="font-bold mb-4">Tu tienda de confianza</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="text-slate-300">Productos verificados</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-slate-300">Entrega rapida</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-500/20 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <span class="text-slate-300">Compra segura</span>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- Products Area --}}
            <div class="flex-1">
                {{-- Search Bar --}}
                <div class="mb-8">
                    <form method="GET" class="relative">
                        <input type="text"
                               name="q"
                               value="{{ $q }}"
                               placeholder="Buscar en esta tienda..."
                               class="w-full pl-14 pr-6 py-4 text-lg bg-white border-2 border-slate-200 rounded-2xl focus:border-[var(--brand-primary)] focus:ring-4 focus:ring-[var(--brand-primary)]/10 transition-all">
                        <svg class="w-6 h-6 text-slate-400 absolute left-5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        @if($categoriaId)
                        <input type="hidden" name="categoria_id" value="{{ $categoriaId }}">
                        @endif
                    </form>
                </div>

                {{-- Results Info --}}
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-slate-800">
                        @if($q)
                            Resultados para "{{ $q }}"
                        @elseif($categoriaId)
                            {{ $categorias->firstWhere('id', $categoriaId)?->nombre ?? 'Productos' }}
                        @else
                            Todos los productos
                        @endif
                    </h2>
                    <span class="text-slate-500">{{ $productos->total() }} productos</span>
                </div>

                {{-- Products Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @forelse($productos as $index => $producto)
                        {{-- Modern Product Card --}}
                        <div class="group bg-white rounded-2xl shadow-sm hover:shadow-premium-lg transition-all duration-300 overflow-hidden border border-slate-100">
                            <div class="relative aspect-square overflow-hidden bg-slate-100">
                                <img src="{{ $producto->display_image ?? $producto->imagen_url ?? '/images/producto-default.svg' }}"
                                     alt="{{ $producto->nombre }}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                     loading="{{ $index > 3 ? 'lazy' : 'eager' }}">

                                {{-- Category Badge --}}
                                @if($producto->categoria)
                                <span class="absolute top-3 left-3 px-3 py-1 bg-white/90 backdrop-blur-sm text-xs font-medium text-slate-700 rounded-full">
                                    {{ $producto->categoria->nombre }}
                                </span>
                                @endif
                            </div>

                            <div class="p-5">
                                <h3 class="font-semibold text-slate-800 mb-2 line-clamp-2 group-hover:text-[var(--brand-primary)] transition-colors">
                                    {{ $producto->nombre }}
                                </h3>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-2xl font-bold" style="color: var(--brand-primary);">
                                        ${{ number_format($producto->precio, 2) }}
                                    </span>
                                    <span class="text-xs text-slate-500 font-medium">por {{ $producto->unidad ?? 'unidad' }}</span>
                                </div>

                                {{-- Quantity Selector + Add Button --}}
                                <div class="flex items-stretch gap-2">
                                    <div class="flex items-center border-2 border-slate-200 rounded-lg overflow-hidden bg-white">
                                        <button type="button"
                                                onclick="const input = this.nextElementSibling; input.value = Math.max(1, parseInt(input.value||1) - 1); input.dispatchEvent(new Event('input'));"
                                                class="px-3 py-2 hover:bg-slate-50 text-slate-600 font-bold transition-colors">
                                            −
                                        </button>
                                        <input type="number"
                                               value="1"
                                               min="1"
                                               step="1"
                                               id="qty_{{ $producto->id }}"
                                               class="w-14 text-center border-0 py-2 focus:ring-0 focus:outline-none font-semibold text-slate-800"
                                               oninput="this.value = Math.max(1, parseInt(this.value) || 1)">
                                        <button type="button"
                                                onclick="const input = this.previousElementSibling; input.value = parseInt(input.value||1) + 1; input.dispatchEvent(new Event('input'));"
                                                class="px-3 py-2 hover:bg-slate-50 text-slate-600 font-bold transition-colors">
                                            +
                                        </button>
                                    </div>
                                    <button type="button"
                                            onclick="const qty = parseInt(document.getElementById('qty_{{ $producto->id }}').value) || 1; storefrontApp().addToCart({{ $producto->id }}, qty, this)"
                                            class="flex-1 px-4 py-2 rounded-lg font-semibold text-white transition-all flex items-center justify-center gap-2 hover:shadow-md"
                                            style="background: var(--brand-primary);">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Agregar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-20">
                            <div class="w-20 h-20 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-800 mb-2">No encontramos productos</h3>
                            <p class="text-slate-500">Intenta con otra busqueda</p>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if($productos->hasPages())
                <div class="mt-12">
                    {{ $productos->links() }}
                </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Trust Banner --}}
    <section class="bg-gradient-to-r from-slate-900 to-slate-800 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center text-white">
                <div>
                    <div class="text-3xl font-bold" style="color: var(--brand-primary);">100%</div>
                    <div class="text-sm text-slate-400 mt-1">Productos verificados</div>
                </div>
                <div>
                    <div class="text-3xl font-bold" style="color: var(--brand-primary);">24h</div>
                    <div class="text-sm text-slate-400 mt-1">Entrega express</div>
                </div>
                <div>
                    <div class="text-3xl font-bold" style="color: var(--brand-primary);">5★</div>
                    <div class="text-sm text-slate-400 mt-1">Calificacion</div>
                </div>
                <div>
                    <div class="text-3xl font-bold" style="color: var(--brand-primary);">+500</div>
                    <div class="text-sm text-slate-400 mt-1">Clientes felices</div>
                </div>
            </div>
        </div>
    </section>
</div>

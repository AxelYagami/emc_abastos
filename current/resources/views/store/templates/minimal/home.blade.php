{{-- 
    MINIMAL TEMPLATE - EMC Abastos Storefront
    Ultra-clean design inspired by Apple/Muji aesthetics
    Lots of white space, elegant typography, no borders, subtle shadows
    Best for: Premium products, boutique stores, design-focused brands
--}}

<div class="min-h-screen bg-white">
    {{-- Minimal Hero - Text focused --}}
    <section class="relative py-20 lg:py-32">
        <div class="max-w-5xl mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-light tracking-tight text-slate-900 leading-tight">
                {{ $currentStore->brand_nombre_publico ?? $currentStore->nombre ?? 'Bienvenido' }}
            </h1>
            @if($currentStore->descripcion)
            <p class="mt-8 text-xl md:text-2xl text-slate-500 font-light max-w-2xl mx-auto leading-relaxed">
                {{ $currentStore->descripcion }}
            </p>
            @endif
            <div class="mt-12">
                <a href="#productos" 
                   class="inline-flex items-center gap-3 text-lg font-medium transition-all group"
                   style="color: var(--brand-primary);">
                    <span>Explorar coleccion</span>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Category Pills (minimal) --}}
    @if($categorias->count() > 0)
    <section class="border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-6 py-6 overflow-x-auto">
            <div class="flex items-center gap-3 min-w-max">
                <a href="{{ url()->current() }}" 
                   class="px-5 py-2.5 rounded-full text-sm font-medium transition-all {{ !$categoriaId ? 'text-white' : 'text-slate-600 hover:bg-slate-100' }}"
                   style="{{ !$categoriaId ? 'background-color: var(--brand-primary)' : '' }}">
                    Todo
                </a>
                @foreach($categorias as $cat)
                <a href="{{ url()->current() }}?categoria_id={{ $cat->id }}" 
                   class="px-5 py-2.5 rounded-full text-sm font-medium transition-all {{ $categoriaId == $cat->id ? 'text-white' : 'text-slate-600 hover:bg-slate-100' }}"
                   style="{{ $categoriaId == $cat->id ? 'background-color: var(--brand-primary)' : '' }}">
                    {{ $cat->nombre }}
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Search (minimal floating) --}}
    <section id="productos" class="max-w-7xl mx-auto px-6 py-12">
        <form method="GET" class="max-w-xl mx-auto mb-16">
            <div class="relative">
                <input type="text"
                       name="q"
                       value="{{ $q }}"
                       placeholder="Buscar..."
                       class="w-full px-6 py-4 text-lg bg-slate-50 border-0 rounded-full focus:ring-2 focus:bg-white transition-all"
                       style="--tw-ring-color: var(--brand-primary);">
                <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 p-2 rounded-full transition-colors" style="color: var(--brand-primary);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </div>
        </form>

        {{-- Products Grid (minimal cards) --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 lg:gap-10">
            @forelse($productos as $index => $producto)
                <a href="#" 
                   onclick="storefrontApp().showProductQuickView({{ $producto->id }}); return false;"
                   class="group block">
                    {{-- Image Container --}}
                    <div class="relative aspect-square bg-slate-50 rounded-2xl overflow-hidden mb-4">
                        <img src="{{ $producto->display_image ?? $producto->imagen_url ?? '/images/producto-default.svg' }}" 
                             alt="{{ $producto->nombre }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                             loading="{{ $index > 7 ? 'lazy' : 'eager' }}">
                        
                        {{-- Quick Add (appears on hover) --}}
                        <div class="absolute inset-x-4 bottom-4 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all">
                            <button type="button"
                                    onclick="event.preventDefault(); event.stopPropagation(); storefrontApp().addToCart({{ $producto->id }}, 1, this)"
                                    class="w-full py-3 text-white text-sm font-medium rounded-xl shadow-lg transition-transform hover:scale-[1.02]"
                                    style="background-color: var(--brand-primary);">
                                Agregar
                            </button>
                        </div>
                    </div>
                    
                    {{-- Product Info (minimal) --}}
                    <h3 class="text-slate-800 font-medium leading-snug group-hover:underline underline-offset-4">
                        {{ $producto->nombre }}
                    </h3>
                    <p class="mt-1 text-lg font-semibold" style="color: var(--brand-primary);">
                        ${{ number_format($producto->precio, 2) }}
                    </p>
                </a>
            @empty
                <div class="col-span-full text-center py-20">
                    <p class="text-slate-400 text-lg">Sin productos disponibles</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination (minimal) --}}
        @if($productos->hasPages())
        <div class="mt-16 flex justify-center">
            {{ $productos->links() }}
        </div>
        @endif
    </section>

    {{-- Minimal Footer Info --}}
    <section class="border-t border-slate-100 py-20">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <div class="grid grid-cols-3 gap-8 text-sm text-slate-500">
                <div>
                    <div class="w-10 h-10 mx-auto mb-3 rounded-full bg-slate-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p>Calidad garantizada</p>
                </div>
                <div>
                    <div class="w-10 h-10 mx-auto mb-3 rounded-full bg-slate-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p>Entrega rapida</p>
                </div>
                <div>
                    <div class="w-10 h-10 mx-auto mb-3 rounded-full bg-slate-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p>Soporte 24/7</p>
                </div>
            </div>
        </div>
    </section>
</div>

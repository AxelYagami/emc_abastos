@props(['producto', 'lazy' => true])

@php
    $imageUrl = $producto->display_image ?? '/images/producto-default.svg';
    $placeholderSvg = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='400' viewBox='0 0 400 400'%3E%3Crect width='400' height='400' fill='%23f3f4f6'/%3E%3Cg fill='%239ca3af'%3E%3Crect x='160' y='140' width='80' height='80' rx='8'/%3E%3Cpath d='M175 165 L185 155 L205 175 L215 165 L225 185 L175 185 Z' fill='%23d1d5db'/%3E%3Ccircle cx='185' cy='160' r='8' fill='%23d1d5db'/%3E%3C/g%3E%3C/svg%3E";
@endphp

<article class="group bg-white rounded-2xl shadow-premium overflow-hidden hover-lift">
    <!-- Product Image -->
    <a href="{{ route('store.producto', $producto) }}"
       class="block aspect-product bg-slate-100 relative overflow-hidden">
        <img src="{{ $imageUrl }}"
             alt="{{ $producto->nombre }}"
             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
             loading="{{ $lazy ? 'lazy' : 'eager' }}"
             width="400"
             height="400"
             onerror="this.onerror=null; this.src='{{ $placeholderSvg }}';">

        <!-- Category Badge -->
        @if($producto->categoria)
            <span class="absolute top-3 left-3 px-3 py-1 text-xs font-semibold rounded-full bg-white/90 backdrop-blur-sm text-slate-700 shadow-sm">
                {{ $producto->categoria->nombre }}
            </span>
        @endif

        <!-- Quick View Overlay -->
        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300 flex items-center justify-center">
            <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 px-4 py-2 bg-white rounded-xl font-semibold text-sm text-slate-800 shadow-lg transform translate-y-2 group-hover:translate-y-0">
                Ver detalles
            </span>
        </div>
    </a>

    <!-- Product Info -->
    <div class="p-4 lg:p-5">
        <a href="{{ route('store.producto', $producto) }}" class="block">
            <h3 class="font-heading font-semibold text-slate-800 group-hover:text-[var(--brand-primary)] transition-colors line-clamp-2 min-h-[2.5rem]">
                {{ $producto->nombre }}
            </h3>
        </a>

        <!-- Price -->
        <div class="mt-2 flex items-baseline gap-2">
            <span class="text-2xl font-bold" style="color: var(--brand-primary);">
                ${{ number_format($producto->precio, 2) }}
            </span>
            @if($producto->unidad)
                <span class="text-sm text-slate-500">/ {{ $producto->unidad }}</span>
            @endif
        </div>

        <!-- Quantity Selector + Add Button -->
        <div class="mt-4 flex items-stretch gap-2">
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
</article>

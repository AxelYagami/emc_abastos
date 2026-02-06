<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{{ $title ?? config('app.name','EMC Abastos') }}</title>
  <meta name="description" content="{{ $metaDescription ?? 'Central de Abastos - Los mejores productos al mejor precio' }}">
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: { 50: '#f0fdf4', 100: '#dcfce7', 500: '#22c55e', 600: '#16a34a', 700: '#15803d', 800: '#166534' },
            secondary: { 50: '#f9fafb', 100: '#f3f4f6', 500: '#6b7280', 600: '#4b5563', 700: '#374151', 800: '#1f2937' }
          }
        }
      }
    }
  </script>
  <style>
    [x-cloak] { display: none !important; }
    .slide-fade-enter { opacity: 0; transform: translateX(20px); }
    .slide-fade-leave { opacity: 0; transform: translateX(-20px); }
  </style>
</head>
<body class="min-h-screen bg-gray-50 flex flex-col">
  <!-- Header con Mini-Carrito -->
  <header class="bg-white border-b shadow-sm sticky top-0 z-50" x-data="miniCart()">
    <div class="max-w-7xl mx-auto px-4 py-3">
      <div class="flex items-center justify-between">
        <a href="{{ route('store.home') }}" class="flex items-center gap-2">
          <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
          </div>
          <span class="text-xl font-bold text-primary-700">EMC Abastos</span>
        </a>

        <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
          <a href="{{ route('store.home') }}" class="text-gray-700 hover:text-primary-600 transition">Tienda</a>
          <a href="{{ route('cart.index') }}" class="text-gray-700 hover:text-primary-600 transition">Carrito</a>
          @auth
            <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-primary-600 transition">Admin</a>
          @else
            <a href="{{ route('login') }}" class="text-gray-700 hover:text-primary-600 transition">Entrar</a>
          @endauth
        </nav>

        <!-- Mini Carrito -->
        <a href="{{ route('cart.index') }}" class="flex items-center gap-2 bg-primary-50 hover:bg-primary-100 px-4 py-2 rounded-full transition">
          <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          <span class="text-primary-700 font-semibold" x-text="itemCount + ' items'">0 items</span>
          <span class="text-primary-600 font-bold" x-text="'$' + total.toFixed(2)">$0.00</span>
        </a>
      </div>
    </div>
  </header>

  <!-- Botón Volver -->
  @if(!request()->routeIs('store.home'))
  <div class="max-w-7xl mx-auto px-4 py-3">
    <button onclick="history.back() || window.location='{{ route('store.home') }}'" class="inline-flex items-center gap-2 text-gray-600 hover:text-primary-600 transition text-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
      </svg>
      Volver
    </button>
  </div>
  @endif

  <!-- Flash Messages -->
  <div class="max-w-7xl mx-auto px-4">
    @if(session('status') || session('ok'))
      <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800 flex items-center gap-2">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('status') ?? session('ok') }}
      </div>
    @endif
    @if(session('error'))
      <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">{{ session('error') }}</div>
    @endif
    @if($errors->any())
      <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
        <ul class="list-disc ml-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      </div>
    @endif
  </div>

  <!-- Content -->
  <main class="flex-1">
    {{ $slot ?? '' }}
    @yield('content')
  </main>

  <!-- Modal Actualizando Tecnología -->
  <div x-data="{ open: false }" class="fixed bottom-4 right-4 z-40">
    <button @click="open = true" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-full shadow-lg flex items-center gap-2 text-sm font-medium transition">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
      Tecnología
    </button>
    <div x-show="open" x-cloak @click.away="open = false" class="absolute bottom-12 right-0 w-80 bg-white rounded-xl shadow-2xl border p-5">
      <h3 class="font-bold text-lg mb-3 text-gray-800">Stack Tecnológico</h3>
      <ul class="space-y-2 text-sm text-gray-600">
        <li class="flex items-center gap-2"><span class="w-2 h-2 bg-primary-500 rounded-full"></span> Laravel 11</li>
        <li class="flex items-center gap-2"><span class="w-2 h-2 bg-primary-500 rounded-full"></span> Tailwind CSS</li>
        <li class="flex items-center gap-2"><span class="w-2 h-2 bg-primary-500 rounded-full"></span> Alpine.js</li>
        <li class="flex items-center gap-2"><span class="w-2 h-2 bg-primary-500 rounded-full"></span> PostgreSQL</li>
      </ul>
      <p class="mt-3 text-xs text-gray-500">Experiencia SPA sin recargas de página</p>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-secondary-800 text-white mt-auto">
    <div class="max-w-7xl mx-auto px-4 py-8">
      <div class="grid md:grid-cols-3 gap-8">
        <div>
          <h4 class="font-bold text-lg mb-3">EMC Abastos</h4>
          <p class="text-gray-400 text-sm">Central de Abastos digital. Los mejores productos al mejor precio.</p>
        </div>
        <div>
          <h4 class="font-bold mb-3">API REST</h4>
          <div class="flex items-center gap-3 text-gray-400">
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
            <div class="text-sm">
              <div class="text-white font-medium">Integración API</div>
              <div>Conecta tu sistema</div>
            </div>
          </div>
        </div>
        <div>
          <h4 class="font-bold mb-3">Desarrollado por</h4>
          <a href="https://wa.me/528318989580" target="_blank" class="flex items-center gap-3 text-gray-400 hover:text-primary-400 transition">
            <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-sm">iaDoS</span>
            </div>
            <div class="text-sm">
              <div class="text-white font-medium">iaDoS.mx</div>
              <div class="flex items-center gap-1">
                <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                8318989580
              </div>
            </div>
          </a>
        </div>
      </div>
      <div class="border-t border-gray-700 mt-6 pt-6 text-center text-gray-500 text-sm">
        © {{ date('Y') }} EMC Abastos · Desarrollado por <a href="https://iados.mx" class="text-primary-400 hover:underline">iaDoS.mx</a>
      </div>
    </div>
  </footer>

  <script>
    function miniCart() {
      return {
        itemCount: {{ (function() { $cart = session('cart', []); if (!is_array($cart)) return 0; $sum = 0; foreach($cart as $item) { if (is_array($item) && isset($item['qty'])) $sum += (int)$item['qty']; } return $sum; })() }},
        total: {{ (function() { $cart = session('cart', []); if (!is_array($cart)) return 0; $sum = 0; foreach($cart as $item) { if (is_array($item) && isset($item['price'], $item['qty'])) $sum += (float)$item['price'] * (int)$item['qty']; } return $sum; })() }}
      }
    }
  </script>
</body>
</html>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Operaciones · EMC' }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
  <header class="bg-white border-b">
    <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
      <div class="font-bold">Operaciones</div>
      <div class="flex items-center gap-2 text-sm">
        <a class="px-3 py-2 rounded hover:bg-gray-100" href="{{ route('ops.hub') }}">Hub</a>
        <a class="px-3 py-2 rounded hover:bg-gray-100" href="{{ route('ops.ordenes.hoy') }}">Lista del día</a>
        <a class="px-3 py-2 rounded hover:bg-gray-100" href="{{ route('admin.dashboard') }}">Admin</a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="px-3 py-2 rounded hover:bg-gray-100">Salir</button>
        </form>
      </div>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 py-6">
    @if(session('ok'))
      <div class="mb-3 p-3 rounded bg-green-50 border border-green-200 text-green-800">{{ session('ok') }}</div>
    @endif
    @if(session('error'))
      <div class="mb-3 p-3 rounded bg-red-50 border border-red-200 text-red-800">{{ session('error') }}</div>
    @endif
    @yield('content')
  </main>
</body>
</html>

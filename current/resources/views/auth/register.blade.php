@extends('layouts.app')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center py-12">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Crear Cuenta</h1>
                @if(isset($currentStore))
                    <p class="text-gray-500 mt-1">Registrate en <strong>{{ $currentStore->nombre }}</strong></p>
                @else
                    <p class="text-gray-500 mt-1">Registrate para hacer pedidos</p>
                @endif
            </div>

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                           placeholder="Tu nombre">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo electronico</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                           placeholder="tu@email.com">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp (opcional)</label>
                    <input type="tel" name="whatsapp" value="{{ old('whatsapp') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                           placeholder="81 1234 5678">
                    @error('whatsapp')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contrasena</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                           placeholder="Minimo 8 caracteres">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-400 text-xs mt-1">Debe incluir mayusculas, minusculas y numeros</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar contrasena</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                           placeholder="Repite tu contrasena">
                </div>

                @if(isset($autoSelectEmpresa))
                    {{-- Auto-selected empresa (from store context) --}}
                    <input type="hidden" name="empresas[]" value="{{ $autoSelectEmpresa }}">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            @if($currentStore && $currentStore->logo_url)
                                <img src="{{ $currentStore->logo_url }}" alt="" class="w-12 h-12 rounded-full object-cover">
                            @else
                                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-bold">
                                    {{ substr($currentStore->nombre ?? 'T', 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-700">Te estas registrando en:</p>
                                <p class="text-lg font-bold text-green-700">{{ $currentStore->nombre ?? 'Esta tienda' }}</p>
                            </div>
                        </div>
                    </div>
                @elseif(isset($empresas) && $empresas->count() > 0)
                    {{-- Multiple empresas selection --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Selecciona tus tiendas favoritas</label>
                        <p class="text-gray-400 text-xs mb-3">Puedes seleccionar una o varias tiendas</p>
                        <div class="space-y-2 max-h-48 overflow-y-auto border rounded-lg p-3">
                            @foreach($empresas as $empresa)
                            <label class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded cursor-pointer">
                                <input type="checkbox" name="empresas[]" value="{{ $empresa->id }}"
                                       class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                                       {{ in_array($empresa->id, old('empresas', [])) ? 'checked' : '' }}>
                                <div class="flex items-center gap-2 flex-1">
                                    @if($empresa->logo_url)
                                        <img src="{{ $empresa->logo_url }}" alt="" class="w-8 h-8 rounded-full object-cover">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-bold text-sm">
                                            {{ substr($empresa->nombre, 0, 1) }}
                                        </div>
                                    @endif
                                    <span class="text-gray-700">{{ $empresa->nombre }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('empresas')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <button type="submit"
                        class="w-full py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                    Crear cuenta
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-500">
                Ya tienes cuenta?
                <a href="{{ route('login') }}" class="text-green-600 hover:underline">Inicia sesion</a>
            </div>
        </div>
    </div>
</div>
@endsection

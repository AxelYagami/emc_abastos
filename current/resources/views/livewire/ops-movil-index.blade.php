<div>
    {{-- Metricas --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        <button wire:click="$set('grupo', 'pendientes')" class="rounded-xl border p-3 text-center transition {{ $grupo === 'pendientes' ? 'bg-yellow-50 border-yellow-300 ring-2 ring-yellow-200' : 'bg-white hover:bg-gray-50' }}">
            <div class="text-2xl font-bold text-yellow-600">{{ $metrics['pendientes'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Pendientes</div>
        </button>
        <button wire:click="$set('grupo', 'proceso')" class="rounded-xl border p-3 text-center transition {{ $grupo === 'proceso' ? 'bg-blue-50 border-blue-300 ring-2 ring-blue-200' : 'bg-white hover:bg-gray-50' }}">
            <div class="text-2xl font-bold text-blue-600">{{ $metrics['preparando'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Preparando</div>
        </button>
        <button wire:click="$set('grupo', 'listas')" class="rounded-xl border p-3 text-center transition {{ $grupo === 'listas' ? 'bg-green-50 border-green-300 ring-2 ring-green-200' : 'bg-white hover:bg-gray-50' }}">
            <div class="text-2xl font-bold text-green-600">{{ $metrics['listas'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Listas / En ruta</div>
        </button>
        <button wire:click="$set('grupo', 'finalizadas')" class="rounded-xl border p-3 text-center transition {{ $grupo === 'finalizadas' ? 'bg-gray-100 border-gray-300 ring-2 ring-gray-200' : 'bg-white hover:bg-gray-50' }}">
            <div class="text-2xl font-bold text-gray-600">{{ $metrics['entregadas'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Entregadas</div>
        </button>
        <div class="rounded-xl border bg-white p-3 text-center">
            <div class="text-2xl font-bold text-red-500">{{ $metrics['canceladas'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Canceladas</div>
        </div>
        <div class="rounded-xl border bg-white p-3 text-center">
            <div class="text-2xl font-bold {{ $metrics['fallos_push'] > 0 ? 'text-orange-500' : 'text-gray-400' }}">{{ $metrics['fallos_push'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Fallos push</div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <div class="flex-1">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar folio, nombre, WhatsApp..."
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300">
        </div>
        <select wire:model.live="tipo" class="border rounded-lg px-3 py-2 text-sm">
            <option value="all">Todos los tipos</option>
            <option value="pickup">Pickup</option>
            <option value="delivery">Delivery</option>
        </select>
        <input wire:model.live="fecha" type="date" class="border rounded-lg px-3 py-2 text-sm">
    </div>

    {{-- Lista de ordenes --}}
    <div class="space-y-3">
        @forelse($ordenes as $orden)
            @php
                $steps = \App\Services\OrderFlowService::stepsFor($orden);
                $currentIdx = array_search($orden->status, $steps);
                $isTerminal = in_array($orden->status, ['entregado', 'cancelado']);
                $tipoEntrega = $orden->tipo_entrega ?? $orden->fulfillment_type ?? 'pickup';
                $nextStatus = app(\App\Services\OrderFlowService::class)->nextStatus($orden);
            @endphp
            <div class="bg-white border rounded-xl p-4 transition hover:shadow-md"
                 x-data="{ expanded: false }"
                 wire:key="orden-{{ $orden->id }}">

                {{-- Header de tarjeta --}}
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-mono font-bold text-sm">{{ $orden->folio ?? '#'.$orden->id }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $tipoEntrega === 'delivery' ? 'bg-purple-100 text-purple-700' : 'bg-teal-100 text-teal-700' }}">
                                {{ $tipoEntrega === 'delivery' ? 'Envio' : 'Pickup' }}
                            </span>
                            @if($orden->status === 'cancelado')
                                <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700">Cancelado</span>
                            @endif
                        </div>
                        <div class="text-sm text-gray-700 mt-1 truncate">{{ $orden->comprador_nombre }}</div>
                        <div class="text-xs text-gray-400">{{ $orden->comprador_whatsapp }} &middot; ${{ number_format($orden->total, 2) }}</div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if(!$isTerminal && $nextStatus)
                            <button wire:click="nextStatus({{ $orden->id }})"
                                    wire:loading.attr="disabled"
                                    class="px-3 py-1.5 bg-black text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition disabled:opacity-50">
                                <span wire:loading.remove wire:target="nextStatus({{ $orden->id }})">
                                    Siguiente
                                </span>
                                <span wire:loading wire:target="nextStatus({{ $orden->id }})">
                                    ...
                                </span>
                            </button>
                        @endif
                        <a href="{{ url('/ops/movil/orden/' . $orden->id) }}"
                           class="px-3 py-1.5 border text-xs font-medium rounded-lg hover:bg-gray-50 transition">
                            Ver
                        </a>
                    </div>
                </div>

                {{-- Stepper visual --}}
                <div class="mt-3 flex items-center gap-1 overflow-x-auto pb-1">
                    @foreach($steps as $i => $step)
                        @php
                            $done = $currentIdx !== false && $i <= $currentIdx;
                            $active = $orden->status === $step;
                            $label = \App\Services\OrderFlowService::statusLabel($step);
                        @endphp
                        <div class="flex items-center gap-1 flex-shrink-0"
                             x-data
                             x-init="$nextTick(() => { if ({{ $active ? 'true' : 'false' }}) $el.querySelector('.step-dot')?.classList.add('animate-pulse') })">
                            <div class="step-dot w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold transition-all duration-300
                                {{ $active ? 'bg-blue-600 text-white ring-2 ring-blue-200 scale-110' : ($done ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400') }}">
                                @if($done && !$active)
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                @else
                                    {{ $i + 1 }}
                                @endif
                            </div>
                            <span class="text-[10px] {{ $active ? 'text-blue-700 font-semibold' : ($done ? 'text-green-700' : 'text-gray-400') }}">
                                {{ $label }}
                            </span>
                            @if(!$loop->last)
                                <div class="w-4 h-0.5 {{ $done ? 'bg-green-400' : 'bg-gray-200' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Repartidor info (delivery) --}}
                @if($tipoEntrega === 'delivery' && $orden->repartidor_nombre)
                    <div class="mt-2 text-xs text-gray-500">
                        Repartidor: <span class="font-medium text-gray-700">{{ $orden->repartidor_nombre }}</span>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-12 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <p>No hay ordenes con estos filtros.</p>
            </div>
        @endforelse
    </div>

    {{-- Paginacion --}}
    <div class="mt-4">{{ $ordenes->links() }}</div>

    {{-- Modal Asignar Repartidor --}}
    @if($showAsignar)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" x-data x-transition>
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6" @click.away="$wire.cancelarAsignar()">
                <h3 class="text-lg font-bold mb-4">Asignar Repartidor</h3>

                @if(count($this->repartidores) > 0)
                    <label class="block text-sm font-medium text-gray-700 mb-1">Seleccionar repartidor</label>
                    <select wire:model="repartidorUserId" class="w-full border rounded-lg px-3 py-2 text-sm mb-3">
                        <option value="0">-- Seleccionar --</option>
                        @foreach($this->repartidores as $rep)
                            <option value="{{ $rep['id'] }}">{{ $rep['name'] }}</option>
                        @endforeach
                    </select>
                    <div class="text-xs text-gray-400 mb-2">O escribe un nombre:</div>
                @endif

                <input wire:model="repartidorNombre" type="text" placeholder="Nombre del repartidor"
                       class="w-full border rounded-lg px-3 py-2 text-sm mb-4">

                <div class="flex gap-2 justify-end">
                    <button wire:click="cancelarAsignar" class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-50">Cancelar</button>
                    <button wire:click="asignarYAvanzar" class="px-4 py-2 bg-black text-white rounded-lg text-sm hover:bg-gray-800">
                        Asignar y enviar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

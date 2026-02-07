<div>
    <div class="mb-4">
        <a href="{{ url('/ops/movil') }}" class="text-sm text-blue-600 hover:underline">&larr; Volver a lista</a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Columna principal --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Header de orden --}}
            <div class="bg-white border rounded-xl p-5">
                <div class="flex items-start justify-between flex-wrap gap-3">
                    <div>
                        <div class="text-xs text-gray-400">Orden</div>
                        <div class="text-2xl font-bold font-mono">{{ $orden->folio ?? '#'.$orden->id }}</div>
                        <div class="text-sm text-gray-600 mt-1">{{ $orden->comprador_nombre }}</div>
                        <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                            @if($orden->comprador_whatsapp)
                                <a href="tel:{{ $orden->comprador_whatsapp }}" class="hover:text-green-600">
                                    {{ $orden->comprador_whatsapp }}
                                </a>
                            @endif
                            <span class="px-2 py-0.5 rounded-full {{ ($orden->tipo_entrega ?? 'pickup') === 'delivery' ? 'bg-purple-100 text-purple-700' : 'bg-teal-100 text-teal-700' }}">
                                {{ ($orden->tipo_entrega ?? 'pickup') === 'delivery' ? 'Delivery' : 'Pickup' }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-400">Total</div>
                        <div class="text-2xl font-bold">${{ number_format($orden->total, 2) }}</div>
                        <div class="text-xs text-gray-400 mt-1">{{ $orden->created_at?->format('d/m/Y H:i') }}</div>
                    </div>
                </div>

                {{-- Stepper grande --}}
                <div class="mt-6">
                    <div class="flex items-center gap-0 overflow-x-auto pb-2">
                        @foreach($steps as $i => $step)
                            @php
                                $currentIdx = array_search($orden->status, $steps);
                                $done = $currentIdx !== false && $i <= $currentIdx;
                                $active = $orden->status === $step;
                            @endphp
                            <div class="flex items-center gap-0 flex-shrink-0">
                                <div class="flex flex-col items-center"
                                     x-data
                                     x-init="$nextTick(() => { if ({{ $active ? 'true' : 'false' }}) $el.querySelector('.dot')?.classList.add('animate-pulse') })">
                                    <div class="dot w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-500
                                        {{ $active ? 'bg-blue-600 text-white ring-4 ring-blue-100 scale-110' : ($done ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400') }}">
                                        @if($done && !$active)
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @else
                                            {{ $i + 1 }}
                                        @endif
                                    </div>
                                    <span class="text-[11px] mt-1 whitespace-nowrap {{ $active ? 'text-blue-700 font-bold' : ($done ? 'text-green-700 font-medium' : 'text-gray-400') }}">
                                        {{ $statusLabels[$step] ?? ucfirst($step) }}
                                    </span>
                                    {{-- Timestamp --}}
                                    @php
                                        $tsMap = ['confirmado'=>'confirmed_at','preparando'=>'preparing_at','listo'=>'ready_at','en_ruta'=>'en_ruta_at','entregado'=>'delivered_at','cancelado'=>'cancelled_at'];
                                        $ts = isset($tsMap[$step]) ? $orden->{$tsMap[$step]} : ($step === 'nuevo' ? $orden->created_at : null);
                                    @endphp
                                    @if($ts)
                                        <span class="text-[9px] text-gray-400">{{ \Carbon\Carbon::parse($ts)->format('H:i') }}</span>
                                    @endif
                                </div>
                                @if(!$loop->last)
                                    <div class="w-8 h-0.5 mx-1 {{ $done && $i < ($currentIdx ?? -1) ? 'bg-green-400' : 'bg-gray-200' }}"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Accion principal --}}
                @if(!in_array($orden->status, ['entregado', 'cancelado']))
                    <div class="mt-4 flex gap-2">
                        <button wire:click="nextStatus" wire:loading.attr="disabled"
                                class="flex-1 px-4 py-3 bg-black text-white font-medium rounded-xl hover:bg-gray-800 transition disabled:opacity-50">
                            <span wire:loading.remove wire:target="nextStatus">
                                Siguiente: {{ $statusLabels[app(\App\Services\OrderFlowService::class)->nextStatus($orden)] ?? 'Avanzar' }}
                            </span>
                            <span wire:loading wire:target="nextStatus">Procesando...</span>
                        </button>
                        <button wire:click="cancelar" wire:confirm="Cancelar esta orden?"
                                class="px-4 py-3 border border-red-200 text-red-600 font-medium rounded-xl hover:bg-red-50 transition">
                            Cancelar
                        </button>
                    </div>
                @else
                    <div class="mt-4 p-3 rounded-lg {{ $orden->status === 'entregado' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }} text-sm font-medium text-center">
                        {{ $orden->status === 'entregado' ? 'Orden entregada' : 'Orden cancelada' }}
                    </div>
                @endif
            </div>

            {{-- Items --}}
            <div class="bg-white border rounded-xl p-5">
                <h3 class="font-bold text-sm mb-3">Productos</h3>
                <div class="divide-y text-sm">
                    @foreach($orden->items as $item)
                        <div class="py-2 flex justify-between items-center">
                            <div>
                                <span class="font-medium">{{ $item->nombre ?? $item->producto?->nombre ?? 'Producto' }}</span>
                                <span class="text-gray-400 ml-1">x{{ $item->cantidad }}</span>
                            </div>
                            <span class="font-medium">${{ number_format($item->total ?? ($item->cantidad * ($item->precio ?? 0)), 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Delivery info --}}
            @if(($orden->tipo_entrega ?? 'pickup') === 'delivery')
                <div class="bg-white border rounded-xl p-5">
                    <h3 class="font-bold text-sm mb-3">Entrega a domicilio</h3>

                    @if($orden->repartidor_nombre)
                        <div class="text-sm mb-2">
                            <span class="text-gray-500">Repartidor:</span>
                            <span class="font-medium">{{ $orden->repartidor_nombre }}</span>
                        </div>
                    @endif

                    @php $meta = $orden->meta ?? []; @endphp
                    @if(!empty($meta['direccion']))
                        <div class="text-sm mb-2">
                            <span class="text-gray-500">Direccion:</span>
                            <span>{{ $meta['direccion'] }}</span>
                        </div>
                        @if(!empty($meta['lat']) && !empty($meta['lng']))
                            <a href="https://maps.google.com/?q={{ $meta['lat'] }},{{ $meta['lng'] }}" target="_blank"
                               class="inline-flex items-center gap-1 text-xs text-blue-600 hover:underline mt-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Abrir en Maps
                            </a>
                        @endif
                    @endif

                    @if($orden->comprador_whatsapp)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $orden->comprador_whatsapp) }}" target="_blank"
                           class="inline-flex items-center gap-1 text-xs text-green-600 hover:underline mt-2">
                            WhatsApp al cliente
                        </a>
                    @endif
                </div>
            @endif
        </div>

        {{-- Columna lateral --}}
        <div class="space-y-6">
            {{-- Pagos (solo lectura) --}}
            <div class="bg-white border rounded-xl p-5">
                <h3 class="font-bold text-sm mb-3">Pagos</h3>
                @if($orden->pagos->isEmpty())
                    <p class="text-sm text-gray-400">Sin pagos registrados.</p>
                @else
                    <div class="space-y-2">
                        @foreach($orden->pagos as $pago)
                            <div class="flex justify-between items-center text-sm border-b pb-2">
                                <div>
                                    <div class="font-medium">{{ ucfirst($pago->metodo) }}</div>
                                    <div class="text-xs text-gray-400">{{ $pago->created_at?->format('H:i') }} &middot; {{ $pago->status }}</div>
                                </div>
                                <span class="font-bold">${{ number_format($pago->monto, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                    @php $totalPagado = $orden->pagos->sum('monto'); @endphp
                    <div class="mt-2 text-xs {{ $totalPagado >= $orden->total ? 'text-green-600' : 'text-orange-600' }}">
                        Pagado: ${{ number_format($totalPagado, 2) }} / ${{ number_format($orden->total, 2) }}
                    </div>
                @endif
            </div>

            {{-- Notificaciones Push --}}
            <div class="bg-white border rounded-xl p-5">
                <h3 class="font-bold text-sm mb-3">Notificaciones Push</h3>
                @if($pushLogs->isEmpty())
                    <p class="text-sm text-gray-400">Sin logs de push.</p>
                @else
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach($pushLogs as $log)
                            <div class="text-xs border-b pb-2">
                                <div class="flex justify-between">
                                    <span class="font-medium {{ $log->status === 'sent' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $log->status }}
                                    </span>
                                    <span class="text-gray-400">{{ $log->created_at?->format('H:i') }}</span>
                                </div>
                                @if($log->error)
                                    <div class="text-red-500 mt-0.5 truncate" title="{{ $log->error }}">{{ $log->error }}</div>
                                @endif
                                @if($log->event_key)
                                    <div class="text-gray-400">{{ $log->event_key }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
                <button wire:click="retryPush" class="mt-3 w-full text-xs border rounded-lg px-3 py-2 hover:bg-gray-50 transition">
                    Reintentar notificacion
                </button>
            </div>

            {{-- Historial de estados --}}
            <div class="bg-white border rounded-xl p-5">
                <h3 class="font-bold text-sm mb-3">Historial</h3>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($statusHistory as $h)
                        <div class="text-xs border-b pb-2">
                            <div class="flex items-center gap-1">
                                <span class="text-gray-400">{{ $h->from_status ?? '—' }}</span>
                                <span class="text-gray-300">&rarr;</span>
                                <span class="font-medium">{{ $h->to_status }}</span>
                            </div>
                            <div class="text-gray-400 mt-0.5">
                                {{ \Carbon\Carbon::parse($h->created_at)->format('d/m H:i') }}
                                @if($h->nota)
                                    &middot; {{ $h->nota }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                    @if(empty($statusHistory))
                        <p class="text-sm text-gray-400">Sin historial.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Asignar Repartidor --}}
    @if($showAsignar)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" x-data x-transition>
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6" @click.away="$wire.cancelarAsignar()">
                <h3 class="text-lg font-bold mb-4">Asignar Repartidor</h3>
                <p class="text-sm text-gray-500 mb-3">Esta orden es delivery. Asigna un repartidor para continuar.</p>

                @if(count($this->repartidores) > 0)
                    <label class="block text-sm font-medium text-gray-700 mb-1">Repartidores disponibles</label>
                    <select wire:model="repartidorUserId" class="w-full border rounded-lg px-3 py-2 text-sm mb-3">
                        <option value="0">-- Seleccionar --</option>
                        @foreach($this->repartidores as $rep)
                            <option value="{{ $rep['id'] }}">{{ $rep['name'] }}</option>
                        @endforeach
                    </select>
                    <div class="text-xs text-gray-400 mb-2">O escribe un nombre externo:</div>
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

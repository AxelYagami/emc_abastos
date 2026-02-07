<?php

namespace App\Livewire;

use App\Models\Orden;
use App\Models\PushNotificationLog;
use App\Services\OrderFlowService;
use Livewire\Component;
use Livewire\WithPagination;

class OpsMovilIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $tipo = 'all';        // all, pickup, delivery
    public string $grupo = 'pendientes'; // pendientes, proceso, listas, finalizadas
    public string $fecha = '';

    // Modal de asignar repartidor
    public bool $showAsignar = false;
    public ?int $asignarOrdenId = null;
    public string $repartidorNombre = '';
    public int $repartidorUserId = 0;

    protected $queryString = ['search', 'tipo', 'grupo'];

    public function mount()
    {
        $this->fecha = now()->toDateString();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTipo()
    {
        $this->resetPage();
    }

    public function updatingGrupo()
    {
        $this->resetPage();
    }

    /**
     * Metricas de ordenes del dia.
     */
    public function getMetricsProperty(): array
    {
        $eid = currentEmpresaId();
        $base = Orden::where('empresa_id', $eid)->whereDate('created_at', $this->fecha);

        return [
            'pendientes'  => (clone $base)->whereIn('status', ['nuevo', 'confirmado'])->count(),
            'preparando'  => (clone $base)->where('status', 'preparando')->count(),
            'listas'      => (clone $base)->whereIn('status', ['listo', 'en_ruta'])->count(),
            'entregadas'  => (clone $base)->where('status', 'entregado')->count(),
            'canceladas'  => (clone $base)->where('status', 'cancelado')->count(),
            'fallos_push' => PushNotificationLog::forEmpresa($eid)->where('status', 'failed')->whereDate('created_at', $this->fecha)->count(),
        ];
    }

    /**
     * Obtener repartidores disponibles para la empresa.
     */
    public function getRepartidoresProperty(): array
    {
        $eid = currentEmpresaId();
        return \Illuminate\Support\Facades\DB::table('empresa_usuario')
            ->join('usuarios', 'usuarios.id', '=', 'empresa_usuario.usuario_id')
            ->join('roles', 'roles.id', '=', 'empresa_usuario.rol_id')
            ->where('empresa_usuario.empresa_id', $eid)
            ->where('empresa_usuario.activo', true)
            ->where('roles.slug', 'repartidor')
            ->select('usuarios.id', 'usuarios.name')
            ->orderBy('usuarios.name')
            ->get()
            ->map(fn($u) => ['id' => $u->id, 'name' => $u->name])
            ->all();
    }

    /**
     * Avanzar al siguiente estado (Modo Simple).
     */
    public function nextStatus(int $ordenId)
    {
        $eid = currentEmpresaId();
        $orden = Orden::where('empresa_id', $eid)->findOrFail($ordenId);
        $flow = app(OrderFlowService::class);

        // Si necesita repartidor, abrir modal
        if ($flow->needsRepartidor($orden)) {
            $this->asignarOrdenId = $ordenId;
            $this->showAsignar = true;
            $this->repartidorNombre = '';
            $this->repartidorUserId = 0;
            return;
        }

        $next = $flow->nextStatus($orden);
        if (!$next) {
            session()->flash('error', 'Esta orden ya esta en estado final.');
            return;
        }

        $fromStatus = $orden->status;
        $flow->applyTransition($orden, $next, auth()->id());

        // WhatsApp notification
        app(\App\Services\WhatsApp\OrderWhatsAppNotifier::class)->onStatusChanged($orden, $fromStatus);

        session()->flash('ok', "Orden #{$orden->folio} → " . OrderFlowService::statusLabel($next));
    }

    /**
     * Asignar repartidor y avanzar a en_ruta.
     */
    public function asignarYAvanzar()
    {
        if (!$this->asignarOrdenId) return;

        $eid = currentEmpresaId();
        $orden = Orden::where('empresa_id', $eid)->findOrFail($this->asignarOrdenId);
        $flow = app(OrderFlowService::class);

        if ($this->repartidorUserId) {
            $user = \App\Models\Usuario::findOrFail($this->repartidorUserId);
            $flow->assignRepartidor($orden, $user->id, $user->name);
        } elseif (trim($this->repartidorNombre) !== '') {
            $orden->update(['repartidor_nombre' => trim($this->repartidorNombre)]);
        } else {
            session()->flash('error', 'Selecciona o escribe el nombre del repartidor.');
            return;
        }

        $fromStatus = $orden->status;
        $flow->applyTransition($orden, 'en_ruta', auth()->id());
        app(\App\Services\WhatsApp\OrderWhatsAppNotifier::class)->onStatusChanged($orden, $fromStatus);

        $this->showAsignar = false;
        $this->asignarOrdenId = null;
        $this->repartidorNombre = '';
        $this->repartidorUserId = 0;

        session()->flash('ok', "Repartidor asignado, orden en ruta.");
    }

    public function cancelarAsignar()
    {
        $this->showAsignar = false;
        $this->asignarOrdenId = null;
    }

    public function render()
    {
        $eid = currentEmpresaId();
        $q = Orden::where('empresa_id', $eid)
            ->whereDate('created_at', $this->fecha)
            ->orderByDesc('id');

        // Filtro tipo entrega
        if ($this->tipo !== 'all') {
            $q->where(function ($qq) {
                $qq->where('tipo_entrega', $this->tipo)
                   ->orWhere('fulfillment_type', $this->tipo);
            });
        }

        // Filtro grupo
        $statusGroups = [
            'pendientes'  => ['nuevo', 'confirmado'],
            'proceso'     => ['preparando'],
            'listas'      => ['listo', 'en_ruta'],
            'finalizadas' => ['entregado', 'cancelado'],
        ];
        if (isset($statusGroups[$this->grupo])) {
            $q->whereIn('status', $statusGroups[$this->grupo]);
        }

        // Busqueda
        if ($this->search !== '') {
            $s = mb_substr(preg_replace('/[%_]+/u', ' ', $this->search), 0, 80);
            $q->where(function ($qq) use ($s) {
                $qq->where('folio', 'ilike', "%{$s}%")
                   ->orWhere('comprador_whatsapp', 'ilike', "%{$s}%")
                   ->orWhere('comprador_nombre', 'ilike', "%{$s}%");
            });
        }

        $ordenes = $q->paginate(20);

        return view('livewire.ops-movil-index', [
            'ordenes' => $ordenes,
            'metrics' => $this->metrics,
        ])->layout('layouts.admin', [
            'title' => 'Ops Movil',
            'header' => 'Centro de Operaciones Movil',
        ]);
    }
}

<?php

namespace App\Livewire;

use App\Models\Orden;
use App\Models\PushNotificationLog;
use App\Services\OrderFlowService;
use Livewire\Component;

class OpsMovilShow extends Component
{
    public int $ordenId;

    // Repartidor modal
    public bool $showAsignar = false;
    public string $repartidorNombre = '';
    public int $repartidorUserId = 0;

    public function mount(int $order)
    {
        $eid = currentEmpresaId();
        $orden = Orden::where('empresa_id', $eid)->findOrFail($order);
        $this->ordenId = $orden->id;
    }

    public function getOrdenProperty(): Orden
    {
        $eid = currentEmpresaId();
        return Orden::where('empresa_id', $eid)
            ->with(['items', 'pagos'])
            ->findOrFail($this->ordenId);
    }

    public function getStatusHistoryProperty(): array
    {
        return \Illuminate\Support\Facades\DB::table('orden_status_histories')
            ->where('empresa_id', currentEmpresaId())
            ->where('orden_id', $this->ordenId)
            ->orderBy('created_at')
            ->get()
            ->all();
    }

    public function getPushLogsProperty()
    {
        return PushNotificationLog::forEmpresa(currentEmpresaId())
            ->where('order_id', $this->ordenId)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }

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
     * Avanzar al siguiente estado.
     */
    public function nextStatus()
    {
        $orden = $this->orden;
        $flow = app(OrderFlowService::class);

        if ($flow->needsRepartidor($orden)) {
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
        app(\App\Services\WhatsApp\OrderWhatsAppNotifier::class)->onStatusChanged($orden, $fromStatus);

        session()->flash('ok', "Estado actualizado → " . OrderFlowService::statusLabel($next));
    }

    /**
     * Asignar repartidor y avanzar.
     */
    public function asignarYAvanzar()
    {
        $orden = $this->orden;
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
        $this->repartidorNombre = '';
        $this->repartidorUserId = 0;

        session()->flash('ok', "Repartidor asignado, orden en ruta.");
    }

    public function cancelarAsignar()
    {
        $this->showAsignar = false;
    }

    /**
     * Reintentar ultima push notification fallida.
     */
    public function retryPush()
    {
        $lastFailed = PushNotificationLog::forEmpresa(currentEmpresaId())
            ->where('order_id', $this->ordenId)
            ->where('status', 'failed')
            ->orderByDesc('created_at')
            ->first();

        if (!$lastFailed || !$lastFailed->payload_json) {
            session()->flash('error', 'No hay notificacion fallida para reintentar.');
            return;
        }

        $roles = ['operaciones', 'admin_empresa', 'superadmin'];
        \App\Jobs\SendPushNotificationJob::dispatch(
            currentEmpresaId(),
            $roles,
            $lastFailed->payload_json,
            $this->ordenId,
            'retry_push'
        );

        session()->flash('ok', 'Reintento de push encolado.');
    }

    /**
     * Cancelar orden.
     */
    public function cancelar()
    {
        $orden = $this->orden;
        if ($orden->status === 'cancelado') return;

        $fromStatus = $orden->status;
        $flow = app(OrderFlowService::class);
        $flow->applyTransition($orden, 'cancelado', auth()->id(), 'Cancelada desde ops movil');
        app(\App\Services\WhatsApp\OrderWhatsAppNotifier::class)->onStatusChanged($orden, $fromStatus);

        session()->flash('ok', 'Orden cancelada.');
    }

    public function render()
    {
        return view('livewire.ops-movil-show', [
            'orden'         => $this->orden,
            'statusHistory' => $this->statusHistory,
            'pushLogs'      => $this->pushLogs,
            'steps'         => OrderFlowService::stepsFor($this->orden),
            'statusLabels'  => OrderFlowService::statusLabels(),
        ])->layout('layouts.admin', [
            'title'  => 'Orden #' . $this->orden->folio,
            'header' => 'Detalle de Orden',
        ]);
    }
}

<?php

namespace App\Services\WhatsApp;

use App\Models\Orden;
use App\Models\WhatsappLog;
use App\Models\VendedorWhatsapp;
use App\Models\Cliente;

class OrderWhatsAppNotifier
{
    public function __construct(private WhatsAppSender $sender) {}

    private function shouldNotifyBuyer(Orden $orden): bool
    {
        if (!$orden->cliente_id) return true;
        $c = Cliente::find($orden->cliente_id);
        return $c ? (bool)$c->enviar_estatus : true;
    }

    public function onCreated(Orden $orden): void
    {
        $this->notifyBuyer($orden, 'orden_creada', "Tu pedido {$orden->folio} fue recibido. Estatus: {$orden->status}");
        $this->notifySellers($orden, 'orden_creada', "Nueva orden {$orden->folio}. Total: $".number_format((float)$orden->total,2));
    }

    public function onStatusChanged(Orden $orden, string $from): void
    {
        $this->notifyBuyer($orden, 'status_changed', "Pedido {$orden->folio}: {$from} → {$orden->status}");
        $this->notifySellers($orden, 'status_changed', "Orden {$orden->folio} cambió: {$from} → {$orden->status}");
    }

    private function notifyBuyer(Orden $orden, string $evento, string $text): void
    {
        if (!$this->shouldNotifyBuyer($orden)) {
            WhatsappLog::create([
                'empresa_id'=>$orden->empresa_id,
                'orden_id'=>$orden->id,
                'evento'=>$evento,
                'to_whatsapp'=>$orden->comprador_whatsapp,
                'status'=>'skipped',
                'skipped_reason'=>'opt_out',
                'payload'=>['text'=>$text,'type'=>'buyer'],
            ]);
            return;
        }

        $log = WhatsappLog::create([
            'empresa_id'=>$orden->empresa_id,
            'orden_id'=>$orden->id,
            'evento'=>$evento,
            'to_whatsapp'=>$orden->comprador_whatsapp,
            'status'=>'queued',
            'payload'=>['text'=>$text,'type'=>'buyer'],
        ]);
        $this->sender->dispatchLog($log);
    }

    private function notifySellers(Orden $orden, string $evento, string $text): void
    {
        $nums = VendedorWhatsapp::where('empresa_id',$orden->empresa_id)->where('activo',true)->pluck('whatsapp')->all();
        foreach ($nums as $to) {
            $log = WhatsappLog::create([
                'empresa_id'=>$orden->empresa_id,
                'orden_id'=>$orden->id,
                'evento'=>$evento,
                'to_whatsapp'=>$to,
                'status'=>'queued',
                'payload'=>['text'=>$text,'type'=>'seller'],
            ]);
            $this->sender->dispatchLog($log);
        }
    }
}

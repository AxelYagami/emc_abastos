<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsappLog;
use App\Models\Empresa;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class WhatsAppSender
{
    /**
     * Dispatch a WhatsApp log entry - actually send the message
     */
    public function dispatchLog(WhatsappLog $log): void
    {
        try {
            // Get empresa for this log to use correct provider settings
            $empresa = $log->empresa_id ? Empresa::find($log->empresa_id) : null;
            $whatsappService = new WhatsAppService($empresa);

            // Extract message from payload
            $payload = $log->payload ?? [];
            $message = $payload['text'] ?? '';

            if (empty($message) || empty($log->to_whatsapp)) {
                $log->status = 'failed';
                $log->error = 'Missing message or phone number';
                $log->save();
                return;
            }

            // Send the message
            $success = $whatsappService->sendText($log->to_whatsapp, $message);

            if ($success) {
                $log->status = 'sent';
                $log->provider_response = [
                    'provider' => $whatsappService->test()['provider'],
                    'sent_at' => now()->toDateTimeString(),
                ];
                $log->error = null;
            } else {
                $log->status = 'failed';
                $log->error = 'Provider returned failure';
                $log->provider_response = [
                    'provider' => $whatsappService->test()['provider'],
                    'failed_at' => now()->toDateTimeString(),
                ];
            }

            $log->save();

            Log::info('WhatsAppSender: Message dispatched', [
                'log_id' => $log->id,
                'status' => $log->status,
                'to' => $log->to_whatsapp,
            ]);

        } catch (\Exception $e) {
            $log->status = 'failed';
            $log->error = $e->getMessage();
            $log->save();

            Log::error('WhatsAppSender: Exception', [
                'log_id' => $log->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Retry a failed log entry
     */
    public function retryLog(WhatsappLog $log): void
    {
        $log->status = 'queued';
        $log->error = null;
        $log->retries = ($log->retries ?? 0) + 1;
        $log->save();

        // Dispatch immediately
        $this->dispatchLog($log);
    }

    /**
     * Get WhatsApp link for manual sending (fallback)
     */
    public function getManualLink(WhatsappLog $log): string
    {
        $empresa = $log->empresa_id ? Empresa::find($log->empresa_id) : null;
        $whatsappService = new WhatsAppService($empresa);

        $payload = $log->payload ?? [];
        $message = $payload['text'] ?? '';

        return $whatsappService->getLink($log->to_whatsapp, $message);
    }
}

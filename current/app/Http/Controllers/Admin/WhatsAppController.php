<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendedorWhatsapp;
use App\Models\WhatsappLog;
use App\Models\Empresa;
use App\Services\WhatsApp\WhatsAppSender;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function index(Request $request)
    {
        $empresaId = (int) $request->session()->get('empresa_id');
        $empresa = Empresa::find($empresaId);

        $numeros = VendedorWhatsapp::where('empresa_id', $empresaId)->orderByDesc('id')->get();
        $logs = WhatsappLog::where('empresa_id', $empresaId)
            ->with('orden')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        return view('admin.whatsapp.index', compact('numeros', 'logs', 'empresa'));
    }

    public function create()
    {
        return view('admin.whatsapp.create');
    }

    public function store(Request $request)
    {
        $empresaId = (int) $request->session()->get('empresa_id');

        $data = $request->validate([
            'whatsapp' => 'required|string|max:30',
            'nombre' => 'nullable|string|max:100',
            'activo' => 'required|boolean',
        ]);

        VendedorWhatsapp::create([
            'empresa_id' => $empresaId,
            'whatsapp' => $data['whatsapp'],
            'nombre' => $data['nombre'] ?? null,
            'activo' => $data['activo'],
        ]);

        return redirect()->route('admin.whatsapp.index')->with('ok', 'Numero guardado');
    }

    public function toggle(Request $request, int $id)
    {
        $empresaId = (int) $request->session()->get('empresa_id');
        $n = VendedorWhatsapp::where('empresa_id', $empresaId)->findOrFail($id);
        $n->activo = !$n->activo;
        $n->save();
        return back()->with('ok', 'Actualizado');
    }

    public function destroy(Request $request, int $id)
    {
        $empresaId = (int) $request->session()->get('empresa_id');
        $n = VendedorWhatsapp::where('empresa_id', $empresaId)->findOrFail($id);
        $n->delete();
        return back()->with('ok', 'Eliminado');
    }

    /**
     * Update WhatsApp provider configuration
     */
    public function updateConfig(Request $request)
    {
        $empresaId = (int) $request->session()->get('empresa_id');
        $empresa = Empresa::findOrFail($empresaId);

        $data = $request->validate([
            'whatsapp_provider' => 'required|string|in:link,callmebot,twilio,waha',
            'whatsapp_api_key' => 'nullable|string|max:500',
        ]);

        // Update empresa settings
        $settings = $empresa->settings ?? [];
        $settings['whatsapp_provider'] = $data['whatsapp_provider'];
        
        if (!empty($data['whatsapp_api_key'])) {
            $settings['whatsapp_api_key'] = $data['whatsapp_api_key'];
        } elseif (isset($settings['whatsapp_api_key'])) {
            unset($settings['whatsapp_api_key']);
        }

        $empresa->settings = $settings;
        $empresa->save();

        return back()->with('ok', 'Configuracion guardada');
    }

    /**
     * Retry a failed WhatsApp log
     */
    public function retry(Request $request, int $id)
    {
        $empresaId = (int) $request->session()->get('empresa_id');
        $log = WhatsappLog::where('empresa_id', $empresaId)->findOrFail($id);

        $sender = new WhatsAppSender();
        $sender->retryLog($log);

        return back()->with('ok', 'Mensaje reenviado');
    }

    /**
     * Send a test message
     */
    public function test(Request $request)
    {
        $empresaId = (int) $request->session()->get('empresa_id');
        $empresa = Empresa::find($empresaId);

        $data = $request->validate([
            'phone' => 'required|string|max:30',
        ]);

        $whatsappService = new \App\Services\WhatsAppService($empresa);
        $success = $whatsappService->sendText($data['phone'], 'Prueba de conexion WhatsApp desde ' . ($empresa->nombre ?? 'EMC Abastos'));

        if ($success) {
            return back()->with('ok', 'Mensaje de prueba enviado');
        } else {
            return back()->with('error', 'No se pudo enviar el mensaje. Verifica la configuracion.');
        }
    }
}

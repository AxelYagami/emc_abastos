<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Orden;
use Illuminate\Http\Request;

class ClientesController extends Controller
{
    public function index(Request $request)
    {
        $empresaId = (int) $request->session()->get('empresa_id');
        $search = trim((string)$request->get('q',''));

        $q = Cliente::where('empresa_id',$empresaId)->orderByDesc('id');
        if ($search !== '') {
            $s = mb_substr(preg_replace('/[%_]+/u',' ', $search), 0, 80);
            $q->where(function($qq) use ($s) {
                $qq->where('nombre','ilike',"%{$s}%")
                   ->orWhere('whatsapp','ilike',"%{$s}%")
                   ->orWhere('email','ilike',"%{$s}%");
            });
        }

        $clientes = $q->paginate(20)->withQueryString();
        return view('admin.clientes.index', compact('clientes','search'));
    }

    public function show(Request $request, int $id)
    {
        $empresaId = (int) $request->session()->get('empresa_id');
        $cliente = Cliente::where('empresa_id',$empresaId)->findOrFail($id);
        $ordenes = Orden::where('empresa_id',$empresaId)->where('cliente_id',$cliente->id)->orderByDesc('id')->limit(50)->get();
        return view('admin.clientes.show', compact('cliente','ordenes'));
    }

    public function toggle(Request $request, int $id)
    {
        $empresaId = (int) $request->session()->get('empresa_id');
        $cliente = Cliente::where('empresa_id',$empresaId)->findOrFail($id);
        $cliente->enviar_estatus = !$cliente->enviar_estatus;
        $cliente->save();
        return back()->with('ok','Actualizado');
    }
}

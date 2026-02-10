<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Traits\AdminContext;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Orden;
use App\Models\Portal;
use Illuminate\Http\Request;

class ClientesController extends Controller
{
    use AdminContext;

    /**
     * Get portal_id from current empresa or session
     */
    private function resolvePortalId(Request $request): ?int
    {
        $empresaId = $this->resolveEmpresaId($request);
        $empresa = Empresa::find($empresaId);
        return $empresa?->portal_id ?? session('current_portal_id');
    }

    public function index(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $portalId = $this->resolvePortalId($request);
        $search = trim((string)$request->get('q',''));

        $q = Cliente::orderByDesc('id');

        // Filter by portal (clientes are shared across portal)
        if ($this->isSuperAdmin()) {
            if ($portalId) {
                $q->where('portal_id', $portalId);
            }
            $q->with('empresa');
        } else {
            // Non-superadmin: filter by their portal
            $q->where('portal_id', $portalId);
        }

        if ($search !== '') {
            $s = mb_substr(preg_replace('/[%_]+/u',' ', $search), 0, 80);
            $q->where(function($qq) use ($s) {
                $qq->where('nombre','ilike',"%{$s}%")
                   ->orWhere('whatsapp','ilike',"%{$s}%")
                   ->orWhere('email','ilike',"%{$s}%");
            });
        }

        $clientes = $q->paginate(20)->withQueryString();
        $empresas = $this->getEmpresasForUser();

        return view('admin.clientes.index', compact('clientes','search','empresas','empresaId'));
    }

    public function create(Request $request)
    {
        return view('admin.clientes.create');
    }

    public function store(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $portalId = $this->resolvePortalId($request);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:200'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:200'],
            'direccion' => ['nullable', 'string', 'max:500'],
            'notas' => ['nullable', 'string', 'max:1000'],
            'enviar_estatus' => ['boolean'],
        ]);

        $cliente = new Cliente();
        $cliente->empresa_id = $empresaId;
        $cliente->portal_id = $portalId;
        $cliente->nombre = $data['nombre'];
        $cliente->whatsapp = $data['whatsapp'] ?? null;
        $cliente->email = $data['email'] ?? null;
        $cliente->direccion = $data['direccion'] ?? null;
        $cliente->notas = $data['notas'] ?? null;
        $cliente->enviar_estatus = $request->boolean('enviar_estatus', true);
        $cliente->save();

        return redirect()->route('admin.clientes.index')->with('ok', 'Cliente creado correctamente');
    }

    public function show(Request $request, int $id)
    {
        $portalId = $this->resolvePortalId($request);
        $cliente = Cliente::where('portal_id', $portalId)->findOrFail($id);
        $ordenes = Orden::where('cliente_id', $cliente->id)->orderByDesc('id')->limit(50)->get();
        return view('admin.clientes.show', compact('cliente','ordenes'));
    }

    public function edit(Request $request, int $id)
    {
        $portalId = $this->resolvePortalId($request);
        $cliente = Cliente::where('portal_id', $portalId)->findOrFail($id);
        return view('admin.clientes.edit', compact('cliente'));
    }

    public function update(Request $request, int $id)
    {
        $portalId = $this->resolvePortalId($request);
        $cliente = Cliente::where('portal_id', $portalId)->findOrFail($id);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:200'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:200'],
            'direccion' => ['nullable', 'string', 'max:500'],
            'notas' => ['nullable', 'string', 'max:1000'],
            'enviar_estatus' => ['boolean'],
        ]);

        $cliente->nombre = $data['nombre'];
        $cliente->whatsapp = $data['whatsapp'] ?? null;
        $cliente->email = $data['email'] ?? null;
        $cliente->direccion = $data['direccion'] ?? null;
        $cliente->notas = $data['notas'] ?? null;
        $cliente->enviar_estatus = $request->boolean('enviar_estatus', true);
        $cliente->save();

        return redirect()->route('admin.clientes.index')->with('ok', 'Cliente actualizado correctamente');
    }

    public function destroy(Request $request, int $id)
    {
        $portalId = $this->resolvePortalId($request);
        $cliente = Cliente::where('portal_id', $portalId)->findOrFail($id);

        // Check if cliente has orders
        $ordenesCount = Orden::where('cliente_id', $cliente->id)->count();
        if ($ordenesCount > 0) {
            return back()->with('error', "No se puede eliminar: el cliente tiene {$ordenesCount} orden(es) asociada(s)");
        }

        $cliente->delete();
        return redirect()->route('admin.clientes.index')->with('ok', 'Cliente eliminado correctamente');
    }

    public function toggle(Request $request, int $id)
    {
        $portalId = $this->resolvePortalId($request);
        $cliente = Cliente::where('portal_id', $portalId)->findOrFail($id);
        $cliente->enviar_estatus = !$cliente->enviar_estatus;
        $cliente->save();
        return back()->with('ok','Actualizado');
    }
}

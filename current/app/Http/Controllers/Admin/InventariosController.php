<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\InventarioMovimiento;
use App\Services\InventarioService;
use Illuminate\Http\Request;

class InventariosController extends Controller
{
    public function index(Request $request, InventarioService $inv)
    {
        $empresaId = (int) $request->session()->get('empresa_id');
        $search = trim((string)$request->get('q',''));

        $q = Producto::where('empresa_id',$empresaId)->orderBy('id');
        if ($search !== '') {
            $s = mb_substr(preg_replace('/[%_]+/u',' ', $search), 0, 80);
            $q->where('nombre','ilike',"%{$s}%");
        }

        $productos = $q->paginate(30)->withQueryString();
        $rows = [];
        foreach ($productos as $p) {
            $rows[] = ['producto'=>$p, 'stock'=>$inv->stock($empresaId, $p->id)];
        }

        return view('admin.inventarios.index', compact('productos','rows','search'));
    }

    public function kardex(Request $request, int $productoId, InventarioService $inv)
    {
        $empresaId = (int) $request->session()->get('empresa_id');
        $producto = Producto::where('empresa_id',$empresaId)->findOrFail($productoId);

        $movs = InventarioMovimiento::where('empresa_id',$empresaId)->where('producto_id',$producto->id)->orderByDesc('id')->limit(200)->get();
        $stock = $inv->stock($empresaId, $producto->id);

        return view('admin.inventarios.kardex', compact('producto','movs','stock'));
    }

    public function ajustar(Request $request, int $productoId, InventarioService $inv)
    {
        $empresaId = (int) $request->session()->get('empresa_id');
        $producto = Producto::where('empresa_id',$empresaId)->findOrFail($productoId);

        $data = $request->validate([
            'tipo' => 'required|in:ajuste,merma,compra',
            'cantidad' => 'required|integer',
            'nota' => 'nullable|string|max:255',
        ]);

        $inv->ajuste($empresaId, $producto->id, (int)$data['cantidad'], $data['tipo'], $data['nota'] ?? null, auth()->id());
        return back()->with('ok','Inventario actualizado');
    }
}

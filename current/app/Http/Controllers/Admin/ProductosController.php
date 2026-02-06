<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductosController extends Controller
{
    public function index(Request $request)
    {
        $empresaId = session('empresa_id');
        $search = trim($request->get('q', ''));

        $query = Producto::where('empresa_id', $empresaId);

        if ($search !== '') {
            $query->where('nombre', 'ilike', "%{$search}%");
        }

        $productos = $query->orderByDesc('id')->paginate(20)->withQueryString();
        return view('admin.productos.index', compact('productos', 'search'));
    }

    public function create()
    {
        $empresaId = session('empresa_id');
        $categorias = Categoria::where('empresa_id', $empresaId)->orderBy('orden')->orderBy('nombre')->get();
        return view('admin.productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $empresaId = session('empresa_id');

        $data = $request->validate([
            'nombre' => ['required','string','max:160'],
            'sku' => ['nullable','string','max:80'],
            'descripcion' => ['nullable','string'],
            'precio' => ['required','numeric','min:0'],
            'stock' => ['nullable','integer','min:0'],
            'categoria_id' => ['nullable','integer', 'exists:categorias,id'],
            'activo' => ['required','boolean'],
        ]);

        if (!empty($data['categoria_id'])) {
            $cat = Categoria::where('id', $data['categoria_id'])->where('empresa_id', $empresaId)->first();
            if (!$cat) {
                return back()->withErrors(['categoria_id' => 'La categoría no pertenece a la empresa actual.'])->withInput();
            }
        }

        $p = new Producto();
        $p->empresa_id = $empresaId;
        $p->nombre = $data['nombre'];
        $p->sku = $data['sku'] ?? null;
        $p->descripcion = $data['descripcion'] ?? null;
        $p->precio = $data['precio'];
        $p->activo = (bool)$data['activo'];
        $p->categoria_id = $data['categoria_id'] ?? null;
        $p->save();

        // Optional: seed stock if your inventario module uses it (handled elsewhere)
        return redirect()->route('admin.productos.index')->with('ok', 'Producto creado');
    }

    public function edit(int $id)
    {
        $empresaId = session('empresa_id');
        $producto = Producto::where('empresa_id', $empresaId)->findOrFail($id);
        $categorias = Categoria::where('empresa_id', $empresaId)->orderBy('orden')->orderBy('nombre')->get();
        return view('admin.productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, int $id)
    {
        $empresaId = session('empresa_id');
        $producto = Producto::where('empresa_id', $empresaId)->findOrFail($id);

        $data = $request->validate([
            'nombre' => ['required','string','max:160'],
            'sku' => ['nullable','string','max:80'],
            'descripcion' => ['nullable','string'],
            'precio' => ['required','numeric','min:0'],
            'categoria_id' => ['nullable','integer', 'exists:categorias,id'],
            'activo' => ['required','boolean'],
        ]);

        if (!empty($data['categoria_id'])) {
            $cat = Categoria::where('id', $data['categoria_id'])->where('empresa_id', $empresaId)->first();
            if (!$cat) {
                return back()->withErrors(['categoria_id' => 'La categoría no pertenece a la empresa actual.'])->withInput();
            }
        }

        $producto->nombre = $data['nombre'];
        $producto->sku = $data['sku'] ?? null;
        $producto->descripcion = $data['descripcion'] ?? null;
        $producto->precio = $data['precio'];
        $producto->activo = (bool)$data['activo'];
        $producto->categoria_id = $data['categoria_id'] ?? null;
        $producto->save();

        return redirect()->route('admin.productos.index')->with('ok', 'Producto actualizado');
    }

    public function destroy(int $id)
    {
        $empresaId = session('empresa_id');
        $producto = Producto::where('empresa_id', $empresaId)->findOrFail($id);
        $producto->delete();

        return redirect()->route('admin.productos.index')->with('ok', 'Producto eliminado');
    }
}

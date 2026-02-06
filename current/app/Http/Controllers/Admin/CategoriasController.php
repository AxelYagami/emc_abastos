<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoriasController extends Controller
{
    public function index(Request $request)
    {
        $empresaId = (int) $request->session()->get('empresa_id');
        $categorias = Categoria::where('empresa_id',$empresaId)->orderByRaw('orden NULLS LAST')->orderBy('id')->get();
        return view('admin.categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('admin.categorias.create');
    }

    public function store(Request $request)
    {
        $empresaId = (int) $request->session()->get('empresa_id');

        $data = $request->validate([
            'nombre'=>'required|string|max:180',
            'slug'=>'nullable|string|max:190',
            'orden'=>'nullable|integer',
            'activa'=>'required|boolean',
        ]);

        $data['empresa_id'] = $empresaId;
        if (empty($data['slug'])) $data['slug'] = Str::slug($data['nombre']);

        Categoria::create($data);
        return redirect()->route('admin.categorias.index')->with('ok','Categoría creada');
    }

    public function edit(Request $request, int $id)
    {
        $empresaId = (int) $request->session()->get('empresa_id');
        $categoria = Categoria::where('empresa_id',$empresaId)->findOrFail($id);
        return view('admin.categorias.edit', compact('categoria'));
    }

    public function update(Request $request, int $id)
    {
        $empresaId = (int) $request->session()->get('empresa_id');
        $categoria = Categoria::where('empresa_id',$empresaId)->findOrFail($id);

        $data = $request->validate([
            'nombre'=>'required|string|max:180',
            'slug'=>'nullable|string|max:190',
            'orden'=>'nullable|integer',
            'activa'=>'required|boolean',
        ]);
        if (empty($data['slug'])) $data['slug'] = Str::slug($data['nombre']);

        $categoria->update($data);
        return redirect()->route('admin.categorias.index')->with('ok','Categoría actualizada');
    }

    public function destroy(Request $request, int $id)
    {
        $empresaId = (int) $request->session()->get('empresa_id');
        $categoria = Categoria::where('empresa_id',$empresaId)->findOrFail($id);
        $categoria->delete();
        return redirect()->route('admin.categorias.index')->with('ok','Categoría eliminada');
    }
}

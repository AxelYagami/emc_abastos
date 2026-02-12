<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Traits\AdminContext;
use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\Producto;
use App\Services\ProductImageService;
use Illuminate\Http\Request;

class ProductosController extends Controller
{
    use AdminContext;

    protected ProductImageService $imageService;

    public function __construct(ProductImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $search = trim($request->get('q', ''));

        $query = Producto::with('categoria');

        // Superadmin: show all or filter by empresa
        if ($this->isSuperAdmin() && !$request->filled('empresa_id')) {
            $query->with('empresa');
        } else {
            $query->where('empresa_id', $empresaId);
        }

        if ($search !== '') {
            $query->where('nombre', 'ilike', "%{$search}%");
        }

        $productos = $query->orderByDesc('id')->paginate(20)->withQueryString();
        $empresas = $this->getEmpresasForUser();

        return view('admin.productos.index', compact('productos', 'search', 'empresas', 'empresaId'));
    }

    public function create(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $categorias = Categoria::where('empresa_id', $empresaId)->orderBy('orden')->orderBy('nombre')->get();
        return view('admin.productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);

        $data = $request->validate([
            'nombre' => ['required','string','max:160'],
            'sku' => ['nullable','string','max:80'],
            'unidad' => ['nullable','string','max:50'],
            'descripcion' => ['nullable','string'],
            'precio' => ['required','numeric','min:0'],
            'stock' => ['nullable','integer','min:0'],
            'categoria_id' => ['nullable','integer', 'exists:categorias,id'],
            'activo' => ['required','boolean'],
            'imagen' => ['nullable','image','max:2048'],
            'imagen_url' => ['nullable','url'],
            'image_source' => ['nullable','in:manual,auto,default'],
        ]);

        if (!empty($data['categoria_id'])) {
            $cat = Categoria::where('id', $data['categoria_id'])->where('empresa_id', $empresaId)->first();
            if (!$cat) {
                return back()->withErrors(['categoria_id' => 'La categoria no pertenece a la empresa actual.'])->withInput();
            }
        }

        $p = new Producto();
        $p->empresa_id = $empresaId;
        $p->nombre = $data['nombre'];
        $p->sku = $data['sku'] ?? null;
        $p->unidad = $data['unidad'] ?? null;
        $p->descripcion = $data['descripcion'] ?? null;
        $p->precio = $data['precio'];
        $p->activo = (bool)$data['activo'];
        $p->categoria_id = $data['categoria_id'] ?? null;
        $p->image_source = $data['image_source'] ?? 'auto';
        $p->use_auto_image = ($data['image_source'] ?? 'auto') === 'auto';
        $p->save();

        // Handle image upload
        $this->handleImageUpload($request, $p, $empresaId);

        return redirect()->route('admin.productos.index')->with('ok', 'Producto creado');
    }

    public function edit(Request $request, Producto $producto)
    {
        $empresaId = $this->resolveEmpresaId($request);

        // Check empresa ownership
        if ($producto->empresa_id !== $empresaId) {
            // If superadmin, offer to switch empresa
            if ($this->isSuperAdmin()) {
                return redirect()
                    ->route('admin.productos.edit', ['producto' => $producto->id, 'empresa_id' => $producto->empresa_id])
                    ->with('info', "Producto pertenece a empresa ID {$producto->empresa_id}. Cambiando contexto...");
            }

            abort(403, 'Este producto no pertenece a tu empresa actual');
        }

        $categorias = Categoria::where('empresa_id', $empresaId)->orderBy('orden')->orderBy('nombre')->get();
        return view('admin.productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $empresaId = $this->resolveEmpresaId($request);

        // Check empresa ownership
        if ($producto->empresa_id !== $empresaId) {
            abort(403, 'Este producto no pertenece a tu empresa actual');
        }

        $data = $request->validate([
            'nombre' => ['required','string','max:160'],
            'sku' => ['nullable','string','max:80'],
            'unidad' => ['nullable','string','max:50'],
            'descripcion' => ['nullable','string'],
            'precio' => ['required','numeric','min:0'],
            'categoria_id' => ['nullable','integer', 'exists:categorias,id'],
            'activo' => ['required','boolean'],
            'imagen' => ['nullable','image','max:2048'],
            'imagen_url' => ['nullable','url'],
            'image_source' => ['nullable','in:manual,auto,default'],
            'is_featured' => ['nullable','boolean'],
        ]);

        if (!empty($data['categoria_id'])) {
            $cat = Categoria::where('id', $data['categoria_id'])->where('empresa_id', $empresaId)->first();
            if (!$cat) {
                return back()->withErrors(['categoria_id' => 'La categoria no pertenece a la empresa actual.'])->withInput();
            }
        }

        $producto->nombre = $data['nombre'];
        $producto->sku = $data['sku'] ?? null;
        $producto->unidad = $data['unidad'] ?? null;
        $producto->descripcion = $data['descripcion'] ?? null;
        $producto->precio = $data['precio'];
        $producto->activo = (bool)$data['activo'];
        $producto->categoria_id = $data['categoria_id'] ?? null;
        $producto->image_source = $data['image_source'] ?? $producto->image_source ?? 'auto';
        $producto->use_auto_image = ($data['image_source'] ?? 'auto') === 'auto';
        $producto->is_featured = $request->boolean('is_featured');
        $producto->save();

        // Handle image upload
        $this->handleImageUpload($request, $producto, $empresaId);

        // Clear image cache
        $this->imageService->clearCache($producto->id);

        return redirect()->route('admin.productos.index')->with('ok', 'Producto actualizado');
    }

    /**
     * Handle image upload for a product
     */
    protected function handleImageUpload(Request $request, Producto $producto, int $empresaId): void
    {
        // Priority: uploaded file > URL > existing
        if ($request->hasFile('imagen')) {
            // Delete old uploaded image if exists
            if ($producto->imagen_path) {
                $oldPath = str_replace('/storage/', '', $producto->imagen_path);
                \Storage::disk('public')->delete($oldPath);
            }

            $imagePath = $this->imageService->uploadImage(
                $request->file('imagen'),
                $empresaId,
                $producto->id
            );
            $producto->imagen_path = $imagePath;
            $producto->imagen_url = null; // Clear URL if file is uploaded
            $producto->image_source = 'manual';
            $producto->use_auto_image = false;
            $producto->save();
        } elseif ($request->filled('imagen_url') && $request->input('image_source') === 'manual') {
            $producto->imagen_url = $request->input('imagen_url');
            $producto->imagen_path = null; // Clear path if URL is provided
            $producto->image_source = 'manual';
            $producto->use_auto_image = false;
            $producto->save();
        }
    }

    public function destroy(Request $request, Producto $producto)
    {
        $empresaId = $this->resolveEmpresaId($request);

        // Check empresa ownership
        if ($producto->empresa_id !== $empresaId) {
            abort(403, 'Este producto no pertenece a tu empresa actual');
        }

        $producto->delete();

        return redirect()->route('admin.productos.index')->with('ok', 'Producto eliminado');
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Request $request, int $id)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $producto = Producto::where('empresa_id', $empresaId)->findOrFail($id);
        $producto->is_featured = !$producto->is_featured;
        $producto->save();

        return back()->with('ok', $producto->is_featured ? 'Producto marcado como destacado' : 'Producto ya no es destacado');
    }
}

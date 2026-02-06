<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class ImportExportController extends Controller
{
    // ======================
    // PRODUCTS IMPORT/EXPORT
    // ======================

    public function productosIndex(Request $request)
    {
        return view('admin.import-export.productos');
    }

    public function productosTemplate()
    {
        $headers = ['sku', 'nombre', 'descripcion', 'precio', 'categoria_nombre', 'activo'];

        $csv = implode(',', $headers) . "\n";
        $csv .= "SKU001,Producto Ejemplo,Descripción del producto,99.99,Categoría Ejemplo,1\n";
        $csv .= "SKU002,Otro Producto,Otra descripción,149.50,Otra Categoría,1\n";

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="plantilla_productos.csv"',
        ]);
    }

    public function productosPreview(Request $request)
    {
        $empresaId = (int) session('empresa_id');

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $file = $request->file('file');
        $rows = [];
        $errors = [];

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $headers = fgetcsv($handle);

            if (!$headers || !in_array('nombre', array_map('strtolower', $headers))) {
                return back()->withErrors(['file' => 'El archivo debe contener al menos la columna "nombre".']);
            }

            $headers = array_map('strtolower', array_map('trim', $headers));
            $lineNum = 1;

            while (($data = fgetcsv($handle)) !== false) {
                $lineNum++;
                if (count($data) !== count($headers)) {
                    $errors[] = "Línea {$lineNum}: Número de columnas incorrecto.";
                    continue;
                }

                $row = array_combine($headers, $data);
                $row['_line'] = $lineNum;
                $row['_errors'] = [];

                // Validate
                if (empty($row['nombre'])) {
                    $row['_errors'][] = 'Nombre requerido';
                }
                if (!empty($row['precio']) && !is_numeric($row['precio'])) {
                    $row['_errors'][] = 'Precio debe ser numérico';
                }

                $rows[] = $row;
            }
            fclose($handle);
        }

        // Get categories for mapping
        $categorias = Categoria::where('empresa_id', $empresaId)
            ->pluck('id', 'nombre')
            ->toArray();

        // Store preview data in session
        session(['import_preview' => $rows, 'import_categorias' => $categorias]);

        $validRows = array_filter($rows, fn($r) => empty($r['_errors']));
        $invalidRows = array_filter($rows, fn($r) => !empty($r['_errors']));

        return view('admin.import-export.productos-preview', compact('rows', 'validRows', 'invalidRows', 'errors', 'categorias'));
    }

    public function productosImport(Request $request)
    {
        $empresaId = (int) session('empresa_id');
        $rows = session('import_preview', []);
        $categoriasMap = session('import_categorias', []);

        if (empty($rows)) {
            return redirect()->route('admin.import-export.productos')
                ->withErrors(['file' => 'No hay datos para importar. Por favor sube el archivo nuevamente.']);
        }

        $imported = 0;
        $updated = 0;
        $skipped = 0;

        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                if (!empty($row['_errors'])) {
                    $skipped++;
                    continue;
                }

                $nombre = trim($row['nombre'] ?? '');
                if (empty($nombre)) {
                    $skipped++;
                    continue;
                }

                // Find or create by SKU or nombre
                $sku = trim($row['sku'] ?? '');
                $producto = null;

                if (!empty($sku)) {
                    $producto = Producto::where('empresa_id', $empresaId)
                        ->where('sku', $sku)
                        ->first();
                }

                if (!$producto) {
                    $producto = Producto::where('empresa_id', $empresaId)
                        ->where('nombre', $nombre)
                        ->first();
                }

                $isNew = !$producto;

                if ($isNew) {
                    $producto = new Producto();
                    $producto->empresa_id = $empresaId;
                }

                $producto->nombre = $nombre;
                $producto->sku = $sku ?: null;
                $producto->descripcion = trim($row['descripcion'] ?? '') ?: null;
                $producto->precio = (float) ($row['precio'] ?? 0);
                $producto->activo = in_array(strtolower($row['activo'] ?? '1'), ['1', 'true', 'si', 'yes', 'activo']);

                // Map categoria
                $catName = trim($row['categoria_nombre'] ?? '');
                if (!empty($catName)) {
                    // Case-insensitive lookup
                    $catId = null;
                    foreach ($categoriasMap as $name => $id) {
                        if (strtolower($name) === strtolower($catName)) {
                            $catId = $id;
                            break;
                        }
                    }
                    $producto->categoria_id = $catId;
                }

                $producto->save();

                if ($isNew) {
                    $imported++;
                } else {
                    $updated++;
                }
            }

            DB::commit();

            // Clear session
            session()->forget(['import_preview', 'import_categorias']);

            return redirect()->route('admin.productos.index')
                ->with('ok', "Importación completada: {$imported} nuevos, {$updated} actualizados, {$skipped} omitidos.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.import-export.productos')
                ->withErrors(['file' => 'Error durante la importación: ' . $e->getMessage()]);
        }
    }

    public function productosExport(Request $request)
    {
        $empresaId = (int) session('empresa_id');

        $productos = Producto::where('empresa_id', $empresaId)
            ->with('categoria')
            ->orderBy('nombre')
            ->get();

        $headers = ['sku', 'nombre', 'descripcion', 'precio', 'categoria_nombre', 'activo'];
        $csv = implode(',', $headers) . "\n";

        foreach ($productos as $p) {
            $csv .= $this->csvEncode($p->sku) . ',';
            $csv .= $this->csvEncode($p->nombre) . ',';
            $csv .= $this->csvEncode($p->descripcion) . ',';
            $csv .= number_format($p->precio, 2, '.', '') . ',';
            $csv .= $this->csvEncode($p->categoria?->nombre) . ',';
            $csv .= ($p->activo ? '1' : '0') . "\n";
        }

        $filename = 'productos_' . date('Y-m-d_His') . '.csv';

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ======================
    // CATEGORIES IMPORT/EXPORT
    // ======================

    public function categoriasIndex(Request $request)
    {
        return view('admin.import-export.categorias');
    }

    public function categoriasTemplate()
    {
        $headers = ['nombre', 'descripcion', 'orden', 'activo'];

        $csv = implode(',', $headers) . "\n";
        $csv .= "Frutas,Frutas frescas y de temporada,1,1\n";
        $csv .= "Verduras,Verduras de alta calidad,2,1\n";

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="plantilla_categorias.csv"',
        ]);
    }

    public function categoriasExport(Request $request)
    {
        $empresaId = (int) session('empresa_id');

        $categorias = Categoria::where('empresa_id', $empresaId)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        $headers = ['nombre', 'descripcion', 'orden', 'activo'];
        $csv = implode(',', $headers) . "\n";

        foreach ($categorias as $c) {
            $csv .= $this->csvEncode($c->nombre) . ',';
            $csv .= $this->csvEncode($c->descripcion) . ',';
            $csv .= ($c->orden ?? 0) . ',';
            $csv .= ($c->activo ? '1' : '0') . "\n";
        }

        $filename = 'categorias_' . date('Y-m-d_His') . '.csv';

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function categoriasImport(Request $request)
    {
        $empresaId = (int) session('empresa_id');

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $file = $request->file('file');
        $imported = 0;
        $updated = 0;

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $headers = fgetcsv($handle);
            $headers = array_map('strtolower', array_map('trim', $headers));

            while (($data = fgetcsv($handle)) !== false) {
                if (count($data) !== count($headers)) continue;

                $row = array_combine($headers, $data);
                $nombre = trim($row['nombre'] ?? '');

                if (empty($nombre)) continue;

                $cat = Categoria::where('empresa_id', $empresaId)
                    ->where('nombre', $nombre)
                    ->first();

                $isNew = !$cat;

                if ($isNew) {
                    $cat = new Categoria();
                    $cat->empresa_id = $empresaId;
                }

                $cat->nombre = $nombre;
                $cat->descripcion = trim($row['descripcion'] ?? '') ?: null;
                $cat->orden = (int) ($row['orden'] ?? 0);
                $cat->activo = in_array(strtolower($row['activo'] ?? '1'), ['1', 'true', 'si', 'yes', 'activo']);
                $cat->save();

                if ($isNew) {
                    $imported++;
                } else {
                    $updated++;
                }
            }
            fclose($handle);
        }

        return redirect()->route('admin.categorias.index')
            ->with('ok', "Importación completada: {$imported} nuevas, {$updated} actualizadas.");
    }

    // ======================
    // HELPERS
    // ======================

    private function csvEncode($value): string
    {
        if ($value === null) return '';

        $value = str_replace('"', '""', $value);

        if (str_contains($value, ',') || str_contains($value, '"') || str_contains($value, "\n")) {
            return '"' . $value . '"';
        }

        return $value;
    }
}

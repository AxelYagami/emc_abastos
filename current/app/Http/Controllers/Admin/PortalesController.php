<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portal;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PortalesController extends Controller
{
    public function index()
    {
        $portales = Portal::withCount('empresas')->orderBy('nombre')->get();
        return view('admin.portales.index', compact('portales'));
    }

    public function create()
    {
        return view('admin.portales.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:160',
            'slug' => 'nullable|string|max:100|unique:portales,slug',
            'dominio' => 'nullable|string|max:255|unique:portales,dominio',
            'logo' => 'nullable|image|max:2048',
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'activo' => 'boolean',
        ]);

        $slug = $data['slug'] ?? Str::slug($data['nombre']);
        $logoPath = null;

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('portales/logos', 'public');
        }

        Portal::create([
            'nombre' => $data['nombre'],
            'slug' => $slug,
            'dominio' => $data['dominio'] ?? null,
            'logo_path' => $logoPath,
            'primary_color' => $data['primary_color'] ?? '#16a34a',
            'secondary_color' => $data['secondary_color'] ?? '#6b7280',
            'activo' => $data['activo'] ?? true,
        ]);

        return redirect()->route('admin.portales.index')->with('ok', 'Portal creado');
    }

    public function edit(int $id)
    {
        $portal = Portal::findOrFail($id);
        return view('admin.portales.edit', compact('portal'));
    }

    public function update(Request $request, int $id)
    {
        $portal = Portal::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required|string|max:160',
            'slug' => 'nullable|string|max:100|unique:portales,slug,' . $id,
            'dominio' => 'nullable|string|max:255|unique:portales,dominio,' . $id,
            'logo' => 'nullable|image|max:2048',
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'activo' => 'boolean',
        ]);

        $logoPath = $portal->logo_path;
        if ($request->hasFile('logo')) {
            if ($logoPath) Storage::disk('public')->delete($logoPath);
            $logoPath = $request->file('logo')->store('portales/logos', 'public');
        }

        $portal->update([
            'nombre' => $data['nombre'],
            'slug' => $data['slug'] ?? $portal->slug,
            'dominio' => $data['dominio'],
            'logo_path' => $logoPath,
            'primary_color' => $data['primary_color'] ?? $portal->primary_color,
            'secondary_color' => $data['secondary_color'] ?? $portal->secondary_color,
            'activo' => $request->boolean('activo'),
        ]);

        return redirect()->route('admin.portales.index')->with('ok', 'Portal actualizado');
    }

    public function destroy(int $id)
    {
        $portal = Portal::findOrFail($id);
        
        if ($portal->empresas()->count() > 0) {
            return back()->with('error', 'No se puede eliminar: tiene empresas asignadas');
        }

        if ($portal->logo_path) {
            Storage::disk('public')->delete($portal->logo_path);
        }
        
        $portal->delete();
        return redirect()->route('admin.portales.index')->with('ok', 'Portal eliminado');
    }
}

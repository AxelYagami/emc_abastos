<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'nombre' => 'required|string|max:200',
            'slug' => 'nullable|string|max:100|unique:portales,slug',
            'dominio' => 'nullable|string|max:255',
            'tagline' => 'nullable|string|max:300',
            'descripcion' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|max:2048',
            'activo' => 'nullable|boolean',
            'active_template' => 'nullable|string|in:classic,modern,minimal,bold,default,market_v2',
            'hero_title' => 'nullable|string|max:200',
            'hero_subtitle' => 'nullable|string|max:300',
            'hero_cta_text' => 'nullable|string|max:50',
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'flyer_enabled' => 'nullable|boolean',
            'flyer_title' => 'nullable|string|max:100',
            'flyer_subtitle' => 'nullable|string|max:200',
            'flyer_max_per_store' => 'nullable|integer|min:1|max:10',
            'flyer_accent_color' => 'nullable|string|max:20',
            'developer_name' => 'nullable|string|max:100',
            'developer_url' => 'nullable|url|max:255',
            'developer_email' => 'nullable|email|max:255',
            'developer_whatsapp' => 'nullable|string|max:20',
            'home_redirect_path' => 'nullable|string|max:100',
            'promos_per_store' => 'nullable|integer|min:1|max:10',
            'show_prices_in_portal' => 'nullable|boolean',
            'ai_assistant_enabled' => 'nullable|boolean',
        ]);

        // Generate slug if empty
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['nombre']);
        }

        // Handle logo upload
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('portales/logos', 'public');
        }

        // Handle booleans
        $data['activo'] = $request->boolean('activo');
        $data['flyer_enabled'] = $request->boolean('flyer_enabled');
        $data['show_prices_in_portal'] = $request->boolean('show_prices_in_portal');
        $data['ai_assistant_enabled'] = $request->boolean('ai_assistant_enabled');

        // Set defaults
        $data['active_template'] = $data['active_template'] ?? 'default';
        $data['flyer_max_per_store'] = $data['flyer_max_per_store'] ?? 5;
        $data['promos_per_store'] = $data['promos_per_store'] ?? 1;
        $data['home_redirect_path'] = $data['home_redirect_path'] ?? 'portal';

        // Remove 'logo' from data (we use logo_path)
        unset($data['logo']);
        
        // Add logo_path if uploaded
        if ($logoPath) {
            $data['logo_path'] = $logoPath;
        }

        Portal::create($data);

        return redirect()->route('admin.portales.index')->with('ok', 'Portal creado correctamente');
    }

    public function edit(int $id)
    {
        $portal = Portal::with('empresas')->findOrFail($id);
        return view('admin.portales.edit', compact('portal'));
    }

    public function update(Request $request, int $id)
    {
        $portal = Portal::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required|string|max:200',
            'slug' => 'nullable|string|max:100|unique:portales,slug,' . $id,
            'dominio' => 'nullable|string|max:255',
            'tagline' => 'nullable|string|max:300',
            'descripcion' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|max:2048',
            'activo' => 'nullable|boolean',
            'active_template' => 'nullable|string|in:classic,modern,minimal,bold,default,market_v2',
            'hero_title' => 'nullable|string|max:200',
            'hero_subtitle' => 'nullable|string|max:300',
            'hero_cta_text' => 'nullable|string|max:50',
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'flyer_enabled' => 'nullable|boolean',
            'flyer_title' => 'nullable|string|max:100',
            'flyer_subtitle' => 'nullable|string|max:200',
            'flyer_max_per_store' => 'nullable|integer|min:1|max:10',
            'flyer_accent_color' => 'nullable|string|max:20',
            'developer_name' => 'nullable|string|max:100',
            'developer_url' => 'nullable|url|max:255',
            'developer_email' => 'nullable|email|max:255',
            'developer_whatsapp' => 'nullable|string|max:20',
            'home_redirect_path' => 'nullable|string|max:100',
            'promos_per_store' => 'nullable|integer|min:1|max:10',
            'show_prices_in_portal' => 'nullable|boolean',
            'ai_assistant_enabled' => 'nullable|boolean',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($portal->logo_path) {
                Storage::disk('public')->delete($portal->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('portales/logos', 'public');
        }

        // Remove 'logo' from data (we use logo_path)
        unset($data['logo']);

        // Handle booleans
        $data['activo'] = $request->boolean('activo');
        $data['flyer_enabled'] = $request->boolean('flyer_enabled');
        $data['show_prices_in_portal'] = $request->boolean('show_prices_in_portal');
        $data['ai_assistant_enabled'] = $request->boolean('ai_assistant_enabled');

        $portal->update($data);

        return redirect()->route('admin.portales.index')->with('ok', 'Portal actualizado correctamente');
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

    /**
     * Switch active portal context for admin session
     */
    public function switchPortal(Request $request)
    {
        $portalId = $request->input('portal_id');
        
        if ($portalId) {
            $portal = Portal::findOrFail($portalId);
            session(['current_portal_id' => $portal->id]);
            return back()->with('ok', "Portal activo: {$portal->nombre}");
        } else {
            session()->forget('current_portal_id');
            return back()->with('ok', 'Mostrando todos los portales');
        }
    }
}

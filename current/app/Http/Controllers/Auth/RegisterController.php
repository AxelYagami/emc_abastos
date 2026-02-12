<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function show(Request $request)
    {
        // Try to get current portal/empresa context
        $portalId = $request->attributes->get('portal_id') ?? session('portal_id');
        $empresaId = $request->attributes->get('store_id') ?? session('empresa_id');
        $currentStore = $request->attributes->get('store');

        // If we have a specific store context, show only that empresa
        if ($empresaId && $currentStore) {
            $empresas = collect([$currentStore]);
            $autoSelectEmpresa = $empresaId;
        } elseif ($portalId) {
            // Show empresas from current portal
            $empresas = Empresa::where('portal_id', $portalId)
                ->where('activa', true)
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'handle', 'logo_url']);
            $autoSelectEmpresa = null;
        } else {
            // Global registration - show all active empresas
            $empresas = Empresa::where('activa', true)
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'handle', 'logo_url']);
            $autoSelectEmpresa = null;
        }

        return view('auth.register', compact('empresas', 'autoSelectEmpresa', 'currentStore'));
    }

    public function register(Request $request)
    {
        // Rate limiting
        $key = 'register:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('error', "Demasiados intentos. Intenta de nuevo en {$seconds} segundos.");
        }
        RateLimiter::hit($key, 300); // 5 attempts per 5 minutes

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'whatsapp' => 'nullable|string|max:20',
            'empresas' => 'nullable|array',
            'empresas.*' => 'exists:empresas,id',
        ]);

        // Capture portal/empresa context from request or session
        $portalId = $request->attributes->get('portal_id') ?? session('portal_id');
        $storeId = $request->attributes->get('store_id') ?? session('empresa_id');
        $currentStore = $request->attributes->get('store');

        $usuario = Usuario::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'whatsapp' => $data['whatsapp'] ?? null,
            'activo' => true,
        ]);

        // Get selected empresas (from form, store context, or session)
        $empresaIds = $data['empresas'] ?? [];

        // If registering from a specific store, auto-associate with that empresa
        if ($storeId && empty($empresaIds)) {
            $empresaIds = [$storeId];
        }

        // If no empresas selected, use session empresa as fallback
        if (empty($empresaIds) && session('empresa_id')) {
            $empresaIds = [session('empresa_id')];
        }

        // Associate user with selected empresas
        if (!empty($empresaIds)) {
            // Get the 'cliente' role ID
            $clienteRol = Rol::where('slug', 'cliente')->first();
            if (!$clienteRol) {
                // Fallback: try to get any customer-like role
                $clienteRol = Rol::where('slug', 'like', '%cliente%')
                    ->orWhere('nombre', 'like', '%cliente%')
                    ->first();
            }

            foreach ($empresaIds as $empresaId) {
                DB::table('empresa_usuario')->insert([
                    'empresa_id' => $empresaId,
                    'usuario_id' => $usuario->id,
                    'rol_id' => $clienteRol?->id,
                    'activo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Set first empresa as active in session
            $request->session()->put('empresa_id', $empresaIds[0]);
        }

        Auth::login($usuario);
        RateLimiter::clear($key);

        // Redirect to the store where they registered
        if ($currentStore && $currentStore->handle) {
            return redirect()->route('store.handle.home', ['handle' => $currentStore->handle])
                ->with('ok', 'Cuenta creada correctamente. Bienvenido a ' . $currentStore->nombre);
        }

        // Fallback to generic store home
        return redirect()->route('store.home')->with('ok', 'Cuenta creada correctamente');
    }
}

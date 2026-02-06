<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
    public function show()
    {
        return view('auth.register');
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
        ]);

        $usuario = Usuario::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'whatsapp' => $data['whatsapp'] ?? null,
            'activo' => true,
        ]);

        // If there's a selected empresa in session, assign as cliente role
        $empresaId = $request->session()->get('empresa_id');
        if ($empresaId) {
            $clienteRol = Rol::where('slug', 'cliente')->first();
            if (!$clienteRol) {
                // Create cliente role if doesn't exist
                $clienteRol = Rol::create([
                    'nombre' => 'Cliente',
                    'slug' => 'cliente',
                    'descripcion' => 'Cliente de la tienda',
                ]);
            }

            DB::table('empresa_usuario')->insert([
                'empresa_id' => $empresaId,
                'usuario_id' => $usuario->id,
                'rol_id' => $clienteRol->id,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Auth::login($usuario);
        RateLimiter::clear($key);

        return redirect()->intended(route('store.home'))->with('ok', 'Cuenta creada correctamente');
    }
}

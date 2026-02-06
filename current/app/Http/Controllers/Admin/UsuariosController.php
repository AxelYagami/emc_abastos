<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Empresa;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsuariosController extends Controller
{
    public function index(Request $request)
    {
        $empresaId = (int) $request->session()->get('empresa_id');
        $user = auth()->user();

        // Superadmin sees all users, others see only their empresa users
        if ($user->isSuperAdmin()) {
            $usuarios = Usuario::orderBy('name')->paginate(30);
        } else {
            $usuarios = Usuario::whereHas('empresas', function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })->orderBy('name')->paginate(30);
        }

        $empresas = Empresa::orderBy('nombre')->get();
        $roles = Rol::orderBy('nombre')->get();

        return view('admin.usuarios.index', compact('usuarios', 'empresas', 'roles'));
    }

    public function create()
    {
        $empresas = Empresa::where('activa', true)->orderBy('nombre')->get();
        $roles = Rol::orderBy('nombre')->get();
        return view('admin.usuarios.create', compact('empresas', 'roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:8|confirmed',
            'whatsapp' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'activo' => 'boolean',
            'empresas' => 'array',
            'empresas.*.empresa_id' => 'required|exists:empresas,id',
            'empresas.*.rol_id' => 'required|exists:roles,id',
        ]);

        $usuario = Usuario::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'whatsapp' => $data['whatsapp'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'activo' => $data['activo'] ?? true,
        ]);

        // Assign to empresas
        if (!empty($data['empresas'])) {
            foreach ($data['empresas'] as $assignment) {
                DB::table('empresa_usuario')->insert([
                    'empresa_id' => $assignment['empresa_id'],
                    'usuario_id' => $usuario->id,
                    'rol_id' => $assignment['rol_id'],
                    'activo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('admin.usuarios.index')->with('ok', 'Usuario creado correctamente');
    }

    public function edit(int $id)
    {
        $usuario = Usuario::findOrFail($id);
        $empresas = Empresa::where('activa', true)->orderBy('nombre')->get();
        $roles = Rol::orderBy('nombre')->get();

        $asignaciones = DB::table('empresa_usuario')
            ->where('usuario_id', $id)
            ->get()
            ->keyBy('empresa_id');

        return view('admin.usuarios.edit', compact('usuario', 'empresas', 'roles', 'asignaciones'));
    }

    public function update(Request $request, int $id)
    {
        $usuario = Usuario::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'whatsapp' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'activo' => 'boolean',
            'empresas' => 'array',
            'empresas.*.empresa_id' => 'required|exists:empresas,id',
            'empresas.*.rol_id' => 'required|exists:roles,id',
            'empresas.*.activo' => 'boolean',
        ]);

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'whatsapp' => $data['whatsapp'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'activo' => $data['activo'] ?? true,
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $usuario->update($updateData);

        // Sync empresa assignments
        DB::table('empresa_usuario')->where('usuario_id', $id)->delete();

        if (!empty($data['empresas'])) {
            foreach ($data['empresas'] as $assignment) {
                DB::table('empresa_usuario')->insert([
                    'empresa_id' => $assignment['empresa_id'],
                    'usuario_id' => $usuario->id,
                    'rol_id' => $assignment['rol_id'],
                    'activo' => $assignment['activo'] ?? true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('admin.usuarios.index')->with('ok', 'Usuario actualizado correctamente');
    }

    public function destroy(int $id)
    {
        $usuario = Usuario::findOrFail($id);

        // Prevent deleting self
        if (auth()->id() === $id) {
            return back()->with('error', 'No puedes eliminarte a ti mismo');
        }

        DB::table('empresa_usuario')->where('usuario_id', $id)->delete();
        $usuario->delete();

        return redirect()->route('admin.usuarios.index')->with('ok', 'Usuario eliminado correctamente');
    }

    public function resetPassword(Request $request, int $id)
    {
        $usuario = Usuario::findOrFail($id);

        $data = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $usuario->update([
            'password' => Hash::make($data['password']),
        ]);

        return back()->with('ok', 'Contraseña actualizada correctamente');
    }

    public function toggle(int $id)
    {
        $usuario = Usuario::findOrFail($id);

        if (auth()->id() === $id) {
            return back()->with('error', 'No puedes desactivarte a ti mismo');
        }

        $usuario->update(['activo' => !$usuario->activo]);

        $status = $usuario->activo ? 'activado' : 'desactivado';
        return back()->with('ok', "Usuario {$status} correctamente");
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Empresa;
use App\Models\Rol;
use App\Services\WhatsAppCredentialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            'password' => 'nullable|string|min:8|confirmed',
            'whatsapp' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'activo' => 'boolean',
            'empresas' => 'array',
            'empresas.*.empresa_id' => 'required|exists:empresas,id',
            'empresas.*.rol_id' => 'required|exists:roles,id',
            'send_whatsapp' => 'boolean',
        ]);

        // Generate password if not provided
        $plainPassword = $data['password'] ?? Str::random(10);

        $usuario = Usuario::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($plainPassword),
            'whatsapp' => $data['whatsapp'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'activo' => $data['activo'] ?? true,
        ]);

        // Assign to empresas
        $firstEmpresa = null;
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

                if (!$firstEmpresa) {
                    $firstEmpresa = Empresa::find($assignment['empresa_id']);
                }
            }
        }

        // Send WhatsApp credentials if requested
        $whatsappSent = false;
        if ($request->boolean('send_whatsapp')) {
            try {
                $whatsappService = new WhatsAppCredentialService();
                $whatsappSent = $whatsappService->sendCredentials($usuario, $plainPassword, $firstEmpresa);
            } catch (\Exception $e) {
                // Log but don't fail
                \Log::warning('Failed to send WhatsApp credentials', ['error' => $e->getMessage()]);
            }
        }

        $message = 'Usuario creado correctamente';
        if ($request->boolean('send_whatsapp')) {
            $message .= $whatsappSent ? ' y credenciales enviadas por WhatsApp' : ' (error al enviar WhatsApp)';
        }

        return redirect()->route('admin.usuarios.index')->with('ok', $message);
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
            'password' => 'nullable|string|min:8|confirmed',
            'send_whatsapp' => 'boolean',
        ]);

        // Generate password if not provided
        $plainPassword = $data['password'] ?? Str::random(10);

        $usuario->update([
            'password' => Hash::make($plainPassword),
        ]);

        // Send WhatsApp if requested
        $whatsappSent = false;
        if ($request->boolean('send_whatsapp')) {
            try {
                // Get first empresa for context
                $empresaId = DB::table('empresa_usuario')
                    ->where('usuario_id', $id)
                    ->value('empresa_id');
                $empresa = $empresaId ? Empresa::find($empresaId) : null;

                $whatsappService = new WhatsAppCredentialService();
                $whatsappSent = $whatsappService->sendPasswordReset($usuario, $plainPassword, $empresa);
            } catch (\Exception $e) {
                \Log::warning('Failed to send WhatsApp password reset', ['error' => $e->getMessage()]);
            }
        }

        $message = 'Contrasena actualizada correctamente';
        if ($request->boolean('send_whatsapp')) {
            $message .= $whatsappSent ? ' y enviada por WhatsApp' : ' (error al enviar WhatsApp)';
        }

        return back()->with('ok', $message);
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

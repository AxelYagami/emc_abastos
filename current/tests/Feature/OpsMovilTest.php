<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\Orden;
use App\Models\PushSubscription;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpsMovilTest extends TestCase
{
    use RefreshDatabase;

    private function createEmpresaAndUser(string $role = 'operaciones'): array
    {
        $empresa = Empresa::create([
            'nombre' => 'Test Empresa',
            'slug' => 'test-empresa',
            'activa' => true,
        ]);

        $user = Usuario::create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
            'activo' => true,
        ]);

        $rol = \Illuminate\Support\Facades\DB::table('roles')
            ->where('slug', $role)->first();

        if (!$rol) {
            $rolId = \Illuminate\Support\Facades\DB::table('roles')->insertGetId([
                'nombre' => ucfirst($role),
                'slug' => $role,
            ]);
        } else {
            $rolId = $rol->id;
        }

        \Illuminate\Support\Facades\DB::table('empresa_usuario')->insert([
            'empresa_id' => $empresa->id,
            'usuario_id' => $user->id,
            'rol_id' => $rolId,
            'activo' => true,
        ]);

        return [$empresa, $user];
    }

    public function test_cannot_view_orden_from_other_empresa(): void
    {
        [$empresa1, $user1] = $this->createEmpresaAndUser();

        $empresa2 = Empresa::create([
            'nombre' => 'Otra Empresa',
            'slug' => 'otra-empresa',
            'activa' => true,
        ]);

        $orden = Orden::create([
            'empresa_id' => $empresa2->id,
            'status' => 'nuevo',
            'total' => 100,
        ]);

        $this->actingAs($user1)
            ->withSession(['empresa_id' => $empresa1->id, 'empresa_nombre' => $empresa1->nombre])
            ->get("/ops/movil/orden/{$orden->id}")
            ->assertStatus(404);
    }

    public function test_push_subscribe_stores_empresa_id(): void
    {
        [$empresa, $user] = $this->createEmpresaAndUser();

        $response = $this->actingAs($user)
            ->withSession(['empresa_id' => $empresa->id])
            ->postJson('/ops/push/subscribe', [
                'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-endpoint-123',
                'keys' => [
                    'p256dh' => 'test-p256dh-key',
                    'auth' => 'test-auth-key',
                ],
            ]);

        $response->assertJson(['ok' => true]);

        $this->assertDatabaseHas('push_subscriptions', [
            'empresa_id' => $empresa->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_unauthenticated_cannot_access_ops_movil(): void
    {
        $this->get('/ops/movil')
            ->assertRedirect('/login');
    }
}

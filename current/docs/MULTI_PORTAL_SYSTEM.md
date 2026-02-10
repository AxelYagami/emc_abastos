# Sistema Multi-Portal - Guía de Implementación

## Descripción General

El sistema Multi-Portal permite crear múltiples marketplaces/portales independientes dentro de la misma instalación de EMC Abastos. Cada portal tiene su propio conjunto de empresas/tiendas y sus datos están completamente aislados.

## Arquitectura

```
Portal 1 (marketplace-a.com)
├── Empresa A
│   ├── Productos
│   ├── Órdenes
│   └── Clientes
└── Empresa B
    ├── Productos
    ├── Órdenes
    └── Clientes

Portal 2 (marketplace-b.com)
├── Empresa C
└── Empresa D
```

## Componentes Implementados

### 1. Modelo Portal (`app/Models/Portal.php`)
- Tabla: `portales`
- Campos: `id`, `nombre`, `slug`, `dominio`, `logo_path`, `primary_color`, `secondary_color`, `settings`, `activo`
- Relaciones: `empresas()`, `configs()`

### 2. PortalScope (`app/Scopes/PortalScope.php`)
Global Scope de Eloquent que filtra automáticamente las consultas por `portal_id`.

**Funcionamiento:**
- Si el modelo tiene columna `portal_id`: filtra directamente
- Si el modelo tiene columna `empresa_id`: filtra a través de la relación con empresa
- Si no hay portal en contexto: no aplica filtro (superadmin ve todo)

### 3. PortalContextService (`app/Services/PortalContextService.php`)
Servicio central para manejar el contexto del portal.

**Métodos principales:**
```php
// Obtener portal actual
PortalContextService::getCurrentPortalId(); // ?int
PortalContextService::getCurrentPortal();   // ?Portal

// Establecer portal
PortalContextService::setCurrentPortal($id);     // Para middleware
PortalContextService::setSessionPortal($id);     // Para sesión admin

// Resolver por dominio
PortalContextService::resolveFromDomain($host);

// Operaciones sin scope
PortalContextService::withoutScope(function() {
    // Consultas sin filtro de portal
});

// Operaciones con portal específico
PortalContextService::withPortal($portalId, function() {
    // Consultas filtradas por ese portal
});
```

### 4. Trait BelongsToPortal (`app/Traits/BelongsToPortal.php`)
Trait que añade el PortalScope a los modelos.

**Uso:**
```php
class Empresa extends Model
{
    use BelongsToPortal;
}
```

**Scopes disponibles:**
```php
// Sin filtro de portal
Empresa::withoutPortalScope()->get();

// Para un portal específico
Empresa::forPortal($portalId)->get();
```

### 5. Middleware ResolvePortalContext (`app/Http/Middleware/ResolvePortalContext.php`)
Middleware que resuelve el portal actual en cada petición.

**Orden de resolución:**
1. Dominio de la petición (ej: `portal1.example.com`)
2. Query parameter (`?portal=slug`)
3. Sesión del admin (si está logueado)
4. Fallback: único portal activo

## Modelos con BelongsToPortal

Los siguientes modelos ya tienen el trait aplicado:

| Modelo | Filtrado por |
|--------|--------------|
| `Empresa` | `portal_id` directo |
| `Producto` | `empresa_id` → `portal_id` |
| `Orden` | `empresa_id` → `portal_id` |
| `Cliente` | `empresa_id` → `portal_id` |
| `Categoria` | `empresa_id` → `portal_id` |
| `Flyer` | `empresa_id` → `portal_id` |
| `WhatsappLog` | `empresa_id` → `portal_id` |
| `StorePromotion` | `empresa_id` → `portal_id` |
| `OrdenPago` | `empresa_id` → `portal_id` |

## Panel de Administración

### Selector de Portal (Superadmin)
- Ubicación: Header del admin panel
- Función: Filtrar todos los datos por portal seleccionado
- "Todos los portales": Sin filtro (ve todo)

### CRUD de Portales
- Ruta: `/admin/portales`
- Solo accesible para superadmin
- Permite crear, editar y eliminar portales

## Uso en Controladores

### Obtener datos filtrados (automático)
```php
// Automáticamente filtrado por portal activo
$empresas = Empresa::where('activa', true)->get();
$productos = Producto::where('activo', true)->get();
```

### Obtener todos los datos (sin filtro)
```php
// Usar scope withoutPortalScope
$todasEmpresas = Empresa::withoutPortalScope()->get();

// O usar el servicio
use App\Services\PortalContextService;

$todas = PortalContextService::withoutScope(function() {
    return Empresa::all();
});
```

### Asignar portal al crear
```php
// El trait auto-asigna el portal_id actual al crear
$empresa = Empresa::create([
    'nombre' => 'Nueva Empresa',
    // portal_id se asigna automáticamente si hay contexto
]);

// O especificar manualmente
$empresa = Empresa::create([
    'nombre' => 'Nueva Empresa',
    'portal_id' => $portalId,
]);
```

## Configuración de Dominios

Cada portal puede tener su propio dominio:

1. **Dominio principal**: Configurar en `portales.dominio`
2. **Subdominio**: El sistema detecta automáticamente por `slug` (ej: `portal1.example.com` busca portal con `slug = 'portal1'`)

### DNS/Servidor Web
Asegurar que todos los dominios/subdominios apunten a la misma instalación de Laravel.

Ejemplo Nginx:
```nginx
server {
    server_name marketplace-a.com marketplace-b.com *.marketplace.com;
    # ... configuración Laravel estándar
}
```

## Consideraciones de Seguridad

1. **Aislamiento de datos**: El PortalScope previene acceso cruzado a datos
2. **Validación**: Siempre verificar permisos del usuario sobre el portal
3. **Superadmin**: Puede ver todos los portales, usar con precaución

## Pruebas

```php
// Test: Crear portal y empresa
$portal = Portal::create(['nombre' => 'Test Portal', 'slug' => 'test']);
PortalContextService::setCurrentPortal($portal->id);

$empresa = Empresa::create(['nombre' => 'Test Empresa', 'activa' => true]);
assert($empresa->portal_id === $portal->id);

// Test: Aislamiento de datos
PortalContextService::setCurrentPortal($otroPortalId);
$empresas = Empresa::all();
assert(!$empresas->contains($empresa)); // No debe contener la empresa del otro portal
```

## Troubleshooting

### "No se muestran datos"
- Verificar que hay un portal activo en sesión
- Comprobar que las empresas tienen `portal_id` asignado
- Superadmin: Seleccionar "Todos los portales" para ver todo

### "El filtro no funciona"
- Asegurar que el modelo usa el trait `BelongsToPortal`
- Verificar que el middleware `ResolvePortalContext` está en el grupo web

### "Error al crear registros"
- Verificar que hay un portal activo o especificar `portal_id` manualmente

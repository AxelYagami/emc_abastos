# EMC Abastos - Sistema Multi-Portal

## Arquitectura Final (Opción A)

```
Portal 1 ─┬─ Empresa A ─── Productos A, Usuarios A, Órdenes A
          └─ Empresa B ─── Productos B, Usuarios B, Órdenes B

Portal 2 ─── Empresa C ─── Productos C, Usuarios C, Órdenes C
```

**Lógica de filtrado:**
- Global Scope **SOLO** en modelo `Empresa` (filtra por `portal_id`)
- Productos, Órdenes, Clientes ya se filtran por `empresa_id` (sistema existente)
- Superadmin sin portal seleccionado ve TODO

## Implementación Completada

### Archivos Creados/Modificados:
1. `app/Scopes/PortalScope.php` - Scope simplificado (solo portal_id directo)
2. `app/Traits/BelongsToPortal.php` - Trait simplificado para Empresa
3. `app/Models/Portal.php` - Modelo con campos de configuración
4. `app/Http/Controllers/Admin/PortalesController.php` - CRUD completo
5. `resources/views/admin/portales/create.blade.php` - Formulario completo
6. `resources/views/admin/portales/edit.blade.php` - Formulario completo
7. `resources/views/admin/portales/index.blade.php` - Listado
8. `resources/views/layouts/admin.blade.php` - Selector de portal en header
9. `database/migrations/2026_02_10_000001_add_config_fields_to_portales.php` - Campos adicionales

### Archivos Eliminados (no necesarios):
- `app/Services/PortalContextService.php`
- `app/Http/Middleware/ResolvePortalContext.php`

### Modelos con Trait:
- **Empresa** ✅ (único modelo con BelongsToPortal)

### Modelos SIN Trait (filtran por empresa_id):
- Producto, Orden, Cliente, Categoria, Flyer, WhatsappLog, StorePromotion, OrdenPago

## Comandos a Ejecutar en Producción

```bash
php artisan migrate
php artisan config:cache
php artisan view:cache
```

## Funcionalidades del CRUD de Portales

Cada portal tiene:
- Información básica (nombre, slug, dominio, tagline, descripción, logo)
- Template (default / market_v2)
- Sección Hero (título, subtítulo, CTA)
- Colores (primario, secundario)
- Flyer/Productos destacados (activado, título, max por tienda)
- Info del desarrollador (footer)
- Configuración general (home redirect, promos por tienda, precios, IA)

## Flujo de Trabajo

1. **Crear Portal** → `/admin/portales/create`
2. **Editar Empresas** → Asignar `portal_id` a cada empresa
3. **Seleccionar Portal** → Header del admin (selector morado)
4. **Ver datos filtrados** → Solo empresas del portal seleccionado

## Estado

✅ Implementación completa
⏳ Pendiente: Ejecutar migraciones en producción

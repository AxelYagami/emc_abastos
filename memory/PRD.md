# EMC Abastos - Multi-Portal System

## Problema Original
Sistema de marketplace multi-tenant que necesita soportar múltiples portales independientes, donde cada portal tiene su propio conjunto de empresas/tiendas con datos completamente aislados.

## Arquitectura Actual
- **Backend**: Laravel (monolito)
- **Frontend Portal**: React SPA
- **Frontend Admin/Tiendas**: Laravel Blade
- **Base de Datos**: PostgreSQL
- **Arquitectura**: Multi-tenant por `empresa_id`, ahora extendido a Multi-Portal con `portal_id`

## Estado Actual: Sistema Multi-Portal IMPLEMENTADO ✅

### Componentes Creados (Diciembre 2025)
1. **PortalScope** (`app/Scopes/PortalScope.php`)
   - Global Scope de Eloquent para filtrado automático
   - Cache de columnas para mejor rendimiento
   - Soporte para filtrado directo (portal_id) e indirecto (vía empresa_id)

2. **PortalContextService** (`app/Services/PortalContextService.php`)
   - Servicio central para gestión del contexto de portal
   - Resolución por dominio/subdominio
   - Métodos helper: `withoutScope()`, `withPortal()`

3. **BelongsToPortal Trait** (`app/Traits/BelongsToPortal.php`)
   - Trait para añadir el scope a modelos
   - Auto-asignación de portal_id en creación
   - Scopes: `withoutPortalScope()`, `forPortal()`

4. **ResolvePortalContext Middleware** (`app/Http/Middleware/ResolvePortalContext.php`)
   - Resolución automática de portal en cada request
   - Prioridad: dominio → query param → sesión → fallback único portal

### Modelos Actualizados
- Empresa ✅
- Producto ✅
- Orden ✅
- Cliente ✅
- Categoria ✅
- Flyer ✅
- WhatsappLog ✅
- StorePromotion ✅
- OrdenPago ✅

### Panel de Administración
- Selector de portal en header (superadmin) ✅
- CRUD de portales ✅
- Selector de portal en formularios de empresas ✅
- Columna de portal en listado de empresas ✅

### Rutas Añadidas
- `POST /admin/portales/switch` - Cambiar portal activo en sesión

## Lo que queda por hacer

### P1 - Testing y Validación
- [ ] Ejecutar pruebas completas del sistema multi-portal
- [ ] Verificar aislamiento de datos entre portales
- [ ] Probar creación de empresas con portal asignado

### P2 - Mejoras Pendientes (del handoff)
- [ ] Verificar configuración de Tailwind CSS Purge
- [ ] Implementar Meta Tags dinámicos para SEO

### P3 - Tareas Futuras
- [ ] Convertir Storefronts de Blade a React
- [ ] Implementar Push Notifications (PWA)

## Documentación
- `/app/current/docs/MULTI_PORTAL_SYSTEM.md` - Guía completa del sistema

## Notas Técnicas
- El middleware `ResolvePortalContext` se ejecuta en todas las rutas web
- Si no hay portal en contexto, el superadmin ve todos los datos
- El fix de SSL para MercadoPago (`'verify' => false`) es temporal
- MercadoPago requiere dominio válido (no funciona con IP)

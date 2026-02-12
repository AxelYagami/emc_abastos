# Configuración de Imágenes Automáticas de Productos

## Problema Resuelto
Las imágenes automáticas ahora usan **APIs reales** que buscan imágenes relevantes al nombre del producto (ej: "plátano" → foto de plátanos).

Ya NO se usan imágenes aleatorias.

---

## Servicios Disponibles

### 1. **Pixabay** (RECOMENDADO) ⭐
- ✅ **GRATIS** - 100 búsquedas/minuto
- ✅ Buena calidad para productos alimenticios
- ✅ Soporte para términos en español
- ✅ Sin marca de agua

**Obtener API Key:**
1. Ir a: https://pixabay.com/api/docs/
2. Crear cuenta gratuita
3. Copiar tu API Key
4. Agregar en `.env`:
   ```env
   IMAGES_SOURCE=pixabay
   PIXABAY_API_KEY=tu_api_key_aqui
   ```

---

### 2. **Pexels** (Alternativa)
- ✅ Gratis - 200 búsquedas/hora
- ✅ Muy buena calidad
- ⚠️ Menos resultados en español

**Obtener API Key:**
1. Ir a: https://www.pexels.com/api/
2. Crear cuenta gratuita
3. Obtener API Key
4. Agregar en `.env`:
   ```env
   IMAGES_SOURCE=pexels
   PEXELS_API_KEY=tu_api_key_aqui
   ```

---

### 3. **Unsplash** (Fotos artísticas)
- ✅ Gratis - 50 búsquedas/hora
- ⚠️ Más artísticas, menos comerciales
- ⚠️ Registro más complejo

**Obtener API Key:**
1. Ir a: https://unsplash.com/developers
2. Crear app
3. Copiar "Access Key"
4. Agregar en `.env`:
   ```env
   IMAGES_SOURCE=unsplash
   UNSPLASH_ACCESS_KEY=tu_access_key_aqui
   ```

---

## Configuración Rápida (5 minutos)

### Opción 1: Pixabay (Recomendado)

1. **Obtener API Key** (2 min):
   - https://pixabay.com/api/docs/
   - Crear cuenta → Ver "API Key" en dashboard

2. **Configurar `.env`**:
   ```env
   IMAGES_SOURCE=pixabay
   PIXABAY_API_KEY=12345678-abc123def456ghi789
   ```

3. **Limpiar cache**:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

4. **Probar**: Ir a admin/productos y actualizar imagen de cualquier producto

---

## Fallback Automático

Si no configuras API key o falla la búsqueda:
1. Intenta con Pixabay (si tiene key)
2. Si falla, usa placeholder genérico con color

---

## Limpiar Cache de Imágenes

Si cambiaste API key o quieres refrescar imágenes:

```bash
php artisan cache:clear
```

O desde código:
```php
app(\App\Services\ProductImageService::class)->clearCache($productoId);
```

---

## Verificar Configuración Actual

En `.env`:
```env
IMAGES_AUTO_FETCH=true
IMAGES_SOURCE=pixabay
PIXABAY_API_KEY=tu_key
```

Cache: 24 horas por defecto (configurable en `config/images.php`)

---

## Orden de Prioridad de Imágenes

1. **Imagen subida** (imagen_path) - Usuario subió foto
2. **Imagen manual** (imagen_url + source=manual) - URL ingresada manualmente
3. **Imagen auto** (use_auto_image=true) - API externa
4. **Imagen default** - /images/producto-default.svg

---

## Troubleshooting

### "Siguen saliendo imágenes aleatorias"
- Verificar que `IMAGES_SOURCE=pixabay` en `.env`
- Verificar que `PIXABAY_API_KEY` esté configurada
- Ejecutar: `php artisan config:clear`
- Limpiar cache del navegador

### "No se cargan imágenes"
- Revisar logs: `storage/logs/laravel.log`
- Verificar límite de API (Pixabay: 100/min)
- Verificar conexión a internet del servidor

### "Quiero cambiar de servicio"
Cambiar `IMAGES_SOURCE` en `.env`:
- `pixabay` (recomendado)
- `pexels`
- `unsplash`
- `placeholder` (sin API, solo placeholders)

---

## Ejemplo de Búsqueda

**Producto:** Plátano Tabasco (Categoría: Frutas, Unidad: kg)

**Query generada:**
- Pixabay: `"platano food fresh alimento fresco"`
- Filtros: `category=food`, `safesearch=true`

**Resultado:** Foto real de plátanos 🍌

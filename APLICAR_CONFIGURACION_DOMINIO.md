# ✅ Configuración Lista - Aplicar Cambios

## Archivos Actualizados

✅ `current/.env` - Agregadas variables SESSION_DOMAIN y SANCTUM_STATEFUL_DOMAINS
✅ `current/config/cors.php` - Configuración CORS creada
✅ `frontend/.env.production` - Backend URL actualizada

---

## Paso 1: Actualizar Frontend `.env`

⚠️ **El archivo `frontend/.env` está protegido contra escritura**

**Solución manual:**

```bash
cd frontend
```

**Editar `.env` con cualquier editor:**
```env
REACT_APP_BACKEND_URL=https://bodegadigital.com.mx
WDS_SOCKET_PORT=443
ENABLE_HEALTH_CHECK=false
```

**O copiar el archivo nuevo:**
```bash
cp .env.production .env
```

---

## Paso 2: Limpiar Cache de Laravel

```bash
cd current
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

## Paso 3: Rebuild Frontend

```bash
cd frontend
npm install
npm run build
```

---

## Paso 4: Verificar Configuración

### A. Verificar variables de entorno

**Backend:**
```bash
cd current
php artisan tinker
>>> config('app.url')
=> "https://bodegadigital.com.mx"
>>> config('cors.allowed_origins')
=> [
     "https://bodegadigital.com.mx",
     "https://www.bodegadigital.com.mx",
   ]
>>> exit
```

**Frontend:**
```bash
cd frontend
cat .env
# Debe mostrar:
# REACT_APP_BACKEND_URL=https://bodegadigital.com.mx
```

### B. Verificar Backend responde

```bash
curl https://bodegadigital.com.mx/api/
# O si tienes un endpoint de health:
curl https://bodegadigital.com.mx/api/health
```

---

## Paso 5: Desplegar

### Si usas servidor local (desarrollo):

**Terminal 1 - Backend:**
```bash
cd current
php artisan serve --host=0.0.0.0 --port=8000
```

**Terminal 2 - Frontend:**
```bash
cd frontend
npm start
```

### Si usas producción (Nginx/Apache):

**Copiar build al servidor:**
```bash
cd frontend
npm run build
# Copiar carpeta 'build' al directorio que sirve Nginx
sudo cp -r build/* /var/www/bodegadigital/public/
```

**Configurar Nginx** (ejemplo):
```nginx
server {
    listen 443 ssl http2;
    server_name bodegadigital.com.mx;

    root /var/www/bodegadigital/frontend/build;
    index index.html;

    # Frontend (React)
    location / {
        try_files $uri $uri/ /index.html;
    }

    # Backend API (Laravel)
    location /api {
        proxy_pass http://127.0.0.1:8000/api;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    # Admin Laravel (Blade)
    location /admin {
        proxy_pass http://127.0.0.1:8000/admin;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

**Reiniciar Nginx:**
```bash
sudo nginx -t
sudo systemctl reload nginx
```

---

## Paso 6: Probar

1. **Abrir navegador:**
   ```
   https://bodegadigital.com.mx
   ```

2. **Abrir DevTools (F12) → Network**

3. **Click en "Ingresar"**

4. **Verificar:**
   - ✅ No debe redirigir a `localhost:8000`
   - ✅ Las peticiones deben ir a `https://bodegadigital.com.mx/api/*`
   - ✅ No debe haber errores CORS en consola

---

## Troubleshooting

### Problema: "Sigue redirigiendo a localhost:8000"

**Causas posibles:**

1. **Frontend .env no actualizado**
   ```bash
   cd frontend
   cat .env | grep BACKEND
   # Debe mostrar: REACT_APP_BACKEND_URL=https://bodegadigital.com.mx
   ```

2. **Build antiguo en caché**
   ```bash
   cd frontend
   rm -rf build node_modules/.cache
   npm run build
   ```

3. **Cache del navegador**
   - Abrir en modo incógnito
   - O limpiar cache (Ctrl+Shift+Delete)

4. **Variables no cargadas**
   ```bash
   # Verificar que la variable se carga en build
   cd frontend
   npm run build
   grep -r "bodegadigital" build/
   # Debe encontrar referencias a bodegadigital.com.mx
   ```

### Problema: "CORS error"

```bash
# Verificar CORS config
cd current
php artisan tinker
>>> config('cors.allowed_origins')
>>> exit

# Debe incluir https://bodegadigital.com.mx
```

Si no aparece:
```bash
php artisan config:clear
php artisan config:cache
```

### Problema: "401 Unauthorized"

1. **Verificar cookies domain:**
   - DevTools → Application → Cookies
   - Domain debe ser `.bodegadigital.com.mx`

2. **Verificar SESSION_DOMAIN:**
   ```bash
   cd current
   grep SESSION_DOMAIN .env
   # Debe ser: SESSION_DOMAIN=.bodegadigital.com.mx (con punto inicial)
   ```

3. **Limpiar sesiones:**
   ```bash
   cd current
   php artisan cache:clear
   rm -rf storage/framework/sessions/*
   ```

---

## Resumen de Comandos Rápidos

```bash
# 1. Actualizar .env del frontend (manual o copiar)
cd frontend
cp .env.production .env

# 2. Limpiar cache Laravel
cd ../current
php artisan config:clear && php artisan cache:clear

# 3. Rebuild frontend
cd ../frontend
npm run build

# 4. Verificar
curl https://bodegadigital.com.mx/api/

# 5. Abrir navegador
# https://bodegadigital.com.mx
```

---

## ¿Necesitas ayuda?

Si después de estos pasos sigues teniendo problemas:

1. Revisar logs de Laravel:
   ```bash
   tail -f current/storage/logs/laravel.log
   ```

2. Revisar console del navegador (F12)

3. Verificar que el backend esté corriendo:
   ```bash
   curl https://bodegadigital.com.mx/api/
   ```

4. Compartir mensajes de error específicos

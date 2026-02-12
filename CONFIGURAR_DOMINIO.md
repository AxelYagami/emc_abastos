# Configuración de Dominio - Portal y Backend

## Problema Actual
- ✅ Portal: `https://bodegadigital.com.mx` (configurado)
- ❌ Frontend apunta a URL incorrecta: `https://portal-isolation.preview.emergentagent.com`
- ❌ Redirige a `http://localhost:8000/login` (URL hardcodeada)

---

## Solución Completa

### Paso 1: Definir Arquitectura

**Opción A: Todo en un dominio** (Recomendado para simplicidad)
```
Frontend: https://bodegadigital.com.mx
Backend:  https://bodegadigital.com.mx/api
```

**Opción B: Subdominios separados** (Mejor para escalabilidad)
```
Frontend: https://bodegadigital.com.mx
Backend:  https://api.bodegadigital.com.mx
```

---

### Paso 2: Configurar Backend Laravel

**A. Editar `current/.env`:**

```env
# Ya configurado ✅
APP_URL=https://bodegadigital.com.mx

# Agregar estas líneas:
SESSION_DOMAIN=.bodegadigital.com.mx
SANCTUM_STATEFUL_DOMAINS=bodegadigital.com.mx,www.bodegadigital.com.mx

# Si usas subdominio API:
# SESSION_DOMAIN=.bodegadigital.com.mx
# SANCTUM_STATEFUL_DOMAINS=bodegadigital.com.mx,api.bodegadigital.com.mx
```

**B. Crear/Editar `current/config/cors.php`:**

```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout', 'register'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://bodegadigital.com.mx',
        'https://www.bodegadigital.com.mx',
        // Agregar si usas subdominio API:
        // 'https://api.bodegadigital.com.mx',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
```

**C. Verificar middleware en `current/app/Http/Kernel.php` o `bootstrap/app.php` (Laravel 11):**

Debe incluir:
```php
\Illuminate\Session\Middleware\StartSession::class,
\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
```

---

### Paso 3: Configurar Frontend React

**A. Editar `frontend/.env`:**

```env
# Si backend está en mismo dominio /api:
REACT_APP_BACKEND_URL=https://bodegadigital.com.mx

# Si backend está en subdominio:
# REACT_APP_BACKEND_URL=https://api.bodegadigital.com.mx

# Configuración de websocket (si aplica)
WDS_SOCKET_PORT=443
ENABLE_HEALTH_CHECK=false
```

**B. Verificar axios/fetch configuración:**

Buscar archivos que configuren axios:
- `frontend/src/api/*`
- `frontend/src/config/*`
- `frontend/src/services/*`

Debe usar: `process.env.REACT_APP_BACKEND_URL`

---

### Paso 4: Configurar Servidor Web

**A. Nginx (Opción A - Todo en un dominio):**

```nginx
server {
    listen 443 ssl http2;
    server_name bodegadigital.com.mx www.bodegadigital.com.mx;

    root /var/www/bodegadigital/frontend/build;
    index index.html;

    # SSL
    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;

    # Frontend (React)
    location / {
        try_files $uri $uri/ /index.html;
    }

    # Backend API (Laravel)
    location /api {
        alias /var/www/bodegadigital/current/public;
        try_files $uri $uri/ @laravel;
    }

    location @laravel {
        rewrite ^/api/(.*)$ /api/$1 break;
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    # Admin Laravel (Blade)
    location /admin {
        alias /var/www/bodegadigital/current/public;
        try_files $uri $uri/ @admin;
    }

    location @admin {
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

**B. Nginx (Opción B - Subdominios):**

```nginx
# Frontend
server {
    listen 443 ssl http2;
    server_name bodegadigital.com.mx;

    root /var/www/bodegadigital/frontend/build;
    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }
}

# Backend API
server {
    listen 443 ssl http2;
    server_name api.bodegadigital.com.mx;

    root /var/www/bodegadigital/current/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

---

### Paso 5: Limpiar Cache y Rebuild

**Backend:**
```bash
cd current
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

**Frontend:**
```bash
cd frontend
npm run build
# O si usas yarn:
yarn build
```

**Reiniciar servicios:**
```bash
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
```

---

## Verificación

### 1. Probar Backend API
```bash
curl https://bodegadigital.com.mx/api/health
# O con subdominio:
curl https://api.bodegadigital.com.mx/api/health
```

### 2. Probar Frontend
- Abrir: `https://bodegadigital.com.mx`
- Abrir DevTools → Network
- Verificar que las peticiones van a la URL correcta del backend

### 3. Probar Login
- Click en "Ingresar"
- Debe redirigir a la URL correcta (NO localhost:8000)

---

## Troubleshooting

### "Sigue redirigiendo a localhost:8000"
1. Limpiar cache del navegador
2. Verificar `frontend/.env` → `REACT_APP_BACKEND_URL`
3. Rebuild del frontend: `npm run build`
4. Buscar hardcoded URLs: `grep -r "localhost:8000" frontend/src/`

### "CORS error"
1. Verificar `current/config/cors.php`
2. Verificar `SANCTUM_STATEFUL_DOMAINS` en `.env`
3. Revisar logs: `current/storage/logs/laravel.log`

### "401 Unauthorized en API"
1. Verificar cookies en DevTools
2. Verificar `SESSION_DOMAIN` incluye el punto: `.bodegadigital.com.mx`
3. Verificar `supports_credentials: true` en CORS

### "Build no actualiza"
1. Borrar `frontend/build/`
2. Limpiar cache de npm: `npm cache clean --force`
3. Rebuild: `npm run build`
4. Verificar que nginx sirve desde el directorio correcto

---

## Resumen de Archivos a Modificar

✅ `current/.env` - APP_URL, SESSION_DOMAIN, SANCTUM_STATEFUL_DOMAINS
✅ `current/config/cors.php` - allowed_origins
✅ `frontend/.env` - REACT_APP_BACKEND_URL
⚙️ Nginx config - proxy_pass, dominios
🔨 Rebuild frontend y limpiar cache

---

## Siguiente Paso

**Por favor indica:**
1. ¿Dónde está alojado tu backend? (mismo dominio /api, subdominio, otro servidor)
2. ¿Qué servidor web usas? (Nginx, Apache, otro)
3. ¿Laravel corre directamente o vía proxy? (php artisan serve, php-fpm, otro)

Con esa info actualizo los archivos específicos.

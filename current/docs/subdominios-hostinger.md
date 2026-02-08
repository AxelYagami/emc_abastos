# Configuración de Dominio y Subdominios - EMC Abastos

## Estructura de URLs

```
emcabastos.com              → Portal principal
www.emcabastos.com          → Portal principal
iados.emcabastos.com        → Tienda iaDoS (subdomain = handle)
guadalupe.emcabastos.com    → Tienda Mercado Guadalupe
tienda-custom.com           → Dominio personalizado (opcional)
```

## Configuración en Hostinger

### 1. Comprar Dominio + Hosting

- **Recomendado:** Business Hosting o Cloud Startup
- Razón: Soporte para SSL Wildcard (*.tudominio.com)

### 2. Configurar DNS

En el panel de Hostinger → DNS Zone:

| Tipo | Nombre | Valor |
|------|--------|-------|
| A | @ | Tu IP del servidor |
| A | www | Tu IP del servidor |
| A | * | Tu IP del servidor |
| CNAME | portal | @ |

### 3. Configurar SSL Wildcard

1. Panel Hostinger → SSL
2. Activar "Wildcard SSL" para *.tudominio.com
3. O usar Let's Encrypt con certbot:
   ```bash
   certbot certonly --manual -d "*.emcabastos.com" -d "emcabastos.com"
   ```

### 4. Configurar Laravel (.env)

```env
APP_URL=https://emcabastos.com
APP_ENV=production
APP_DEBUG=false

# Base domain para subdominios
SESSION_DOMAIN=.emcabastos.com
SANCTUM_STATEFUL_DOMAINS=emcabastos.com,*.emcabastos.com
```

### 5. Portal Config (Admin)

En Admin → Portal Central → Configuración:
- **Fallback Domain:** emcabastos.com

---

## Cómo Funciona

### Resolución de Tiendas

El middleware `ResolveStoreContext` detecta automáticamente:

1. **Subdominios:** `iados.emcabastos.com`
   - Extrae "iados" como subdomain
   - Busca empresa con `handle = 'iados'`

2. **Dominios personalizados:** `mi-tienda.com`
   - Busca en tabla `store_domains`
   - O en `empresas.domain`

3. **Rutas:** `/t/iados`
   - Usa el parámetro `{handle}` de la ruta

---

## Agregar Nueva Tienda

### Opción A: Solo con subdomain (automático)

1. Crear empresa en Admin con `handle: mitienda`
2. Ya funciona: `mitienda.emcabastos.com`

### Opción B: Dominio personalizado

1. Admin → Empresas → Editar → Dominios
2. Agregar dominio: `mitienda.com`
3. En DNS de ese dominio, apuntar A record a tu servidor
4. Agregar SSL para ese dominio

---

## Nginx Config (si usas VPS)

```nginx
server {
    listen 80;
    listen 443 ssl;
    
    # Wildcard - captura todos los subdominios
    server_name emcabastos.com *.emcabastos.com;
    
    # SSL Wildcard
    ssl_certificate /etc/letsencrypt/live/emcabastos.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/emcabastos.com/privkey.pem;
    
    root /var/www/emc_abastos/current/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## Apache Config (.htaccess)

Ya incluido en Laravel. Solo asegúrate que:
- `mod_rewrite` está activo
- `AllowOverride All` en el VirtualHost

---

## Troubleshooting

### Subdomain no resuelve a la tienda

1. Verificar que el `handle` de la empresa coincida con el subdomain
2. Revisar que la empresa esté activa
3. Limpiar caché: `php artisan cache:clear`

### SSL no funciona en subdominios

1. Verificar que tienes SSL Wildcard
2. O generar certificado individual para ese subdomain

### Sesión se pierde entre dominios

Configurar en `.env`:
```
SESSION_DOMAIN=.emcabastos.com
```
(nota el punto inicial)

---

## Ejemplo Completo

```
Empresa: iaDoS
- handle: iados
- activa: true

URLs que funcionan:
✅ iados.emcabastos.com
✅ emcabastos.com/t/iados
✅ midominio.com (si configurado en store_domains)
```

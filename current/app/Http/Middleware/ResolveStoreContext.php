<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\PortalConfig;
use App\Models\StoreDomain;

/**
 * Resolve store context from:
 * 1. Subdomain (tienda.tudominio.com)
 * 2. Custom domain (tienda.com)
 * 3. Handle route parameter (/t/{handle})
 * 4. Session fallback
 */
class ResolveStoreContext
{
    public function handle(Request $request, Closure $next)
    {
        $store = null;
        $host = strtolower($request->getHost());
        $baseDomain = $this->getBaseDomain();

        // 1. Try subdomain first (*.tudominio.com)
        if (!$store && $baseDomain) {
            $subdomain = $this->extractSubdomain($host, $baseDomain);
            if ($subdomain && $subdomain !== 'www' && $subdomain !== 'portal') {
                // Try to find by handle (subdomain = handle)
                $store = Empresa::with('portal')->where('handle', $subdomain)
                    ->where('activa', true)
                    ->first();
                
                // Or try StoreDomain
                if (!$store) {
                    $storeDomain = StoreDomain::findByDomain($host);
                    $store = $storeDomain?->empresa?->load('portal');
                }
            }
        }

        // 2. Try custom domain (tienda.com)
        if (!$store && !$this->isMainAppDomain($host)) {
            $storeDomain = StoreDomain::findByDomain($host);
            $store = $storeDomain?->empresa?->load('portal');
            
            // Or find by domain directly on empresa
            if (!$store) {
                $store = Empresa::findByDomain($host)?->load('portal');
            }
        }

        // 3. Try handle from route parameter (/t/{handle})
        if (!$store && $request->route('handle')) {
            $store = Empresa::findByHandle($request->route('handle'))?->load('portal');
        }

        // 4. Fallback to session (backward compatibility)
        if (!$store && session('empresa_id')) {
            $store = Empresa::with('portal')->find(session('empresa_id'));
        }

        // Set store context
        if ($store) {
            $request->attributes->set('store', $store);
            $request->attributes->set('store_id', $store->id);

            session(['empresa_id' => $store->id]);
            session(['empresa_nombre' => $store->nombre]);
            session(['store_handle' => $store->handle]);

            view()->share('currentStore', $store);
            view()->share('storeContext', true);
        }

        return $next($request);
    }

    /**
     * Get base domain from config
     * Example: emcabastos.com
     */
    private function getBaseDomain(): ?string
    {
        // First try portal config
        $fallback = PortalConfig::get('fallback_domain');
        if ($fallback) return strtolower($fallback);

        // Then try APP_URL
        $appUrl = config('app.url');
        if ($appUrl) {
            $parsed = parse_url($appUrl, PHP_URL_HOST);
            if ($parsed) return strtolower($parsed);
        }

        return null;
    }

    /**
     * Extract subdomain from host
     * Example: tienda.emcabastos.com → tienda
     */
    private function extractSubdomain(string $host, string $baseDomain): ?string
    {
        $baseDomain = strtolower($baseDomain);
        $host = strtolower($host);

        // Check if host ends with base domain
        if (str_ends_with($host, '.' . $baseDomain)) {
            $subdomain = str_replace('.' . $baseDomain, '', $host);
            return $subdomain ?: null;
        }

        return null;
    }

    /**
     * Check if this is the main app domain (not a store)
     */
    private function isMainAppDomain(string $host): bool
    {
        $host = strtolower($host);
        $baseDomain = $this->getBaseDomain();

        $mainDomains = [
            'localhost',
            '127.0.0.1',
            $baseDomain,
            'www.' . $baseDomain,
            'portal.' . $baseDomain,
        ];

        return in_array($host, array_filter($mainDomains));
    }
}

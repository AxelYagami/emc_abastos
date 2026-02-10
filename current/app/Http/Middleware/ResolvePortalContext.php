<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\PortalContextService;

class ResolvePortalContext
{
    /**
     * Handle an incoming request.
     *
     * Este middleware resuelve el portal actual basándose en:
     * 1. El dominio de la petición
     * 2. Un parámetro de query string (?portal=slug)
     * 3. La sesión del admin (si está logueado)
     */
    public function handle(Request $request, Closure $next): Response
    {
        $portal = null;

        try {
            // 1. Try to resolve from domain/subdomain
            $host = $request->getHost();
            $portal = PortalContextService::resolveFromDomain($host);

            // 2. If no portal from domain, try query parameter (for testing/preview)
            if (!$portal && $request->has('portal')) {
                $slug = $request->query('portal');
                $portal = \App\Models\Portal::where('slug', $slug)->where('activo', true)->first();
            }

            // 3. If still no portal and user is logged in, check session
            if (!$portal && auth()->check()) {
                $sessionPortalId = session('current_portal_id');
                if ($sessionPortalId) {
                    $portal = \App\Models\Portal::find($sessionPortalId);
                }
            }

            // 4. Fallback: if only one active portal exists, use it
            if (!$portal) {
                $activePortals = \App\Models\Portal::where('activo', true)->get();
                if ($activePortals->count() === 1) {
                    $portal = $activePortals->first();
                }
            }
        } catch (\Exception $e) {
            // Table doesn't exist yet - migrations pending, continue without portal context
            $portal = null;
        }

        // Set portal in context
        if ($portal) {
            PortalContextService::setCurrentPortal($portal->id);
            
            // Share portal with views
            view()->share('currentPortal', $portal);
            
            // Add portal info to request for controllers
            $request->attributes->set('portal', $portal);
            $request->attributes->set('portal_id', $portal->id);
        }

        return $next($request);
    }
}

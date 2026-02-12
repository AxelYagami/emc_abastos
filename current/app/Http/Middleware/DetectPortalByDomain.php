<?php

namespace App\Http\Middleware;

use App\Models\Portal;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DetectPortalByDomain
{
    /**
     * Handle an incoming request.
     *
     * Detects the current domain and loads the corresponding portal configuration.
     * This enables multi-tenant functionality where each portal can have its own custom domains.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the current domain from the request
        $currentDomain = $request->getHost();

        Log::info("DetectPortalByDomain middleware ejecutándose", [
            'current_domain' => $currentDomain,
            'full_url' => $request->fullUrl()
        ]);

        // Try to find a portal that has this domain in its 'dominios' array
        $portal = Portal::where('activo', true)
            ->get()
            ->first(function ($p) use ($currentDomain) {
                if (!$p->dominios || !is_array($p->dominios)) {
                    return false;
                }
                // Check if current domain matches any of the portal's domains
                return in_array($currentDomain, $p->dominios, true);
            });

        if ($portal) {
            // Portal found for this domain
            // Store portal context in session and request attributes
            $request->session()->put('portal_id', $portal->id);
            $request->session()->put('current_portal_slug', $portal->slug);
            $request->attributes->set('portal_id', $portal->id);
            $request->attributes->set('portal', $portal);

            Log::info("✓ Portal detectado por dominio", [
                'domain' => $currentDomain,
                'portal_id' => $portal->id,
                'portal_slug' => $portal->slug
            ]);
        } else {
            Log::info("✗ NO se detectó portal para este dominio", [
                'domain' => $currentDomain
            ]);
        }

        return $next($request);
    }
}

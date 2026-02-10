<?php

namespace App\Services;

use App\Models\Portal;
use Illuminate\Support\Facades\Cache;

class PortalContextService
{
    /**
     * Key for storing portal context in session/request
     */
    protected const CONTEXT_KEY = 'current_portal_id';
    
    /**
     * Cache duration for portal lookup (in seconds)
     */
    protected const CACHE_TTL = 3600;

    /**
     * Get the current portal ID from context
     */
    public static function getCurrentPortalId(): ?int
    {
        // First check request context (set by middleware)
        if (app()->has(self::CONTEXT_KEY)) {
            return app(self::CONTEXT_KEY);
        }

        // Then check session (for admin users)
        if (session()->has(self::CONTEXT_KEY)) {
            return session(self::CONTEXT_KEY);
        }

        return null;
    }

    /**
     * Get the current portal model
     */
    public static function getCurrentPortal(): ?Portal
    {
        $portalId = self::getCurrentPortalId();
        
        if (!$portalId) {
            return null;
        }

        return Cache::remember("portal:{$portalId}", self::CACHE_TTL, function () use ($portalId) {
            return Portal::find($portalId);
        });
    }

    /**
     * Set the current portal in context (for middleware)
     */
    public static function setCurrentPortal(?int $portalId): void
    {
        if ($portalId) {
            app()->instance(self::CONTEXT_KEY, $portalId);
        } else {
            app()->forgetInstance(self::CONTEXT_KEY);
        }
    }

    /**
     * Set the current portal in session (for admin users)
     */
    public static function setSessionPortal(?int $portalId): void
    {
        if ($portalId) {
            session([self::CONTEXT_KEY => $portalId]);
        } else {
            session()->forget(self::CONTEXT_KEY);
        }
    }

    /**
     * Resolve portal from domain
     */
    public static function resolveFromDomain(string $domain): ?Portal
    {
        // Remove port if present
        $domain = preg_replace('/:\d+$/', '', $domain);
        
        try {
            // Check cache first
            return Cache::remember("portal_domain:{$domain}", self::CACHE_TTL, function () use ($domain) {
                // Try exact domain match
                $portal = Portal::where('dominio', $domain)->where('activo', true)->first();
                
                if ($portal) {
                    return $portal;
                }

                // Try subdomain match (e.g., portal1.example.com -> portal1)
                $parts = explode('.', $domain);
                if (count($parts) >= 2) {
                    $subdomain = $parts[0];
                    $portal = Portal::where('slug', $subdomain)->where('activo', true)->first();
                    if ($portal) {
                        return $portal;
                    }
                }

                return null;
            });
        } catch (\Exception $e) {
            // Table doesn't exist yet - return null
            return null;
        }
    }

    /**
     * Clear portal cache
     */
    public static function clearCache(?int $portalId = null, ?string $domain = null): void
    {
        if ($portalId) {
            Cache::forget("portal:{$portalId}");
        }
        if ($domain) {
            Cache::forget("portal_domain:{$domain}");
        }
    }

    /**
     * Execute callback without portal scope (for superadmin operations)
     */
    public static function withoutScope(callable $callback)
    {
        $currentPortalId = self::getCurrentPortalId();
        self::setCurrentPortal(null);
        
        try {
            return $callback();
        } finally {
            self::setCurrentPortal($currentPortalId);
        }
    }

    /**
     * Execute callback with specific portal
     */
    public static function withPortal(int $portalId, callable $callback)
    {
        $currentPortalId = self::getCurrentPortalId();
        self::setCurrentPortal($portalId);
        
        try {
            return $callback();
        } finally {
            self::setCurrentPortal($currentPortalId);
        }
    }
}

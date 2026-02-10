<?php

namespace App\Traits;

use App\Scopes\PortalScope;

/**
 * Trait for Empresa model to filter by portal_id
 */
trait BelongsToPortal
{
    public static function bootBelongsToPortal(): void
    {
        static::addGlobalScope(new PortalScope());

        // Auto-assign portal_id on create
        static::creating(function ($model) {
            if (!$model->portal_id && session('current_portal_id')) {
                $model->portal_id = session('current_portal_id');
            }
        });
    }

    /**
     * Scope to query without portal restriction
     */
    public function scopeWithoutPortalScope($query)
    {
        return $query->withoutGlobalScope(PortalScope::class);
    }
}

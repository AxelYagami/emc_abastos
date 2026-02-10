<?php

namespace App\Traits;

use App\Scopes\PortalScope;
use App\Services\PortalContextService;

/**
 * Trait for models that belong to a Portal (directly or through Empresa)
 * 
 * Use this trait on models that should be filtered by portal:
 * - Models with direct portal_id column (like Empresa)
 * - Models with empresa_id that should inherit portal filtering
 */
trait BelongsToPortal
{
    /**
     * Boot the trait
     */
    public static function bootBelongsToPortal(): void
    {
        // Add global scope to automatically filter queries
        static::addGlobalScope(new PortalScope());

        // Auto-assign portal_id on create for models with direct portal_id
        static::creating(function ($model) {
            if ($model->hasPortalIdColumn() && !$model->portal_id) {
                $model->portal_id = PortalContextService::getCurrentPortalId();
            }
        });
    }

    /**
     * Check if this model has a portal_id column
     */
    public function hasPortalIdColumn(): bool
    {
        return in_array('portal_id', $this->getFillable()) 
            || $this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), 'portal_id');
    }

    /**
     * Scope to query without portal restriction
     */
    public function scopeWithoutPortalScope($query)
    {
        return $query->withoutGlobalScope(PortalScope::class);
    }

    /**
     * Scope to query for a specific portal
     */
    public function scopeForPortal($query, int $portalId)
    {
        return $query->withoutGlobalScope(PortalScope::class)
            ->where(function ($q) use ($portalId) {
                if ($this->hasPortalIdColumn()) {
                    $q->where($this->getTable() . '.portal_id', $portalId);
                } elseif (in_array('empresa_id', $this->getFillable())) {
                    $q->whereHas('empresa', function ($eq) use ($portalId) {
                        $eq->where('portal_id', $portalId);
                    });
                }
            });
    }
}

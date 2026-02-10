<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PortalScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     * Solo filtra modelos que tienen portal_id directamente (como Empresa)
     */
    public function apply(Builder $builder, Model $model): void
    {
        $portalId = session('current_portal_id');
        
        // Si no hay portal en contexto, no filtramos (superadmin ve todo)
        if (!$portalId) {
            return;
        }

        // Solo filtrar si el modelo tiene portal_id
        $builder->where($model->getTable() . '.portal_id', $portalId);
    }
}

<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Services\PortalContextService;

class PortalScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * El scope filtra automáticamente los registros por el portal_id del contexto actual.
     * Solo se aplica si:
     * 1. Hay un portal activo en el contexto
     * 2. El modelo tiene la columna portal_id (directa o a través de empresa)
     */
    public function apply(Builder $builder, Model $model): void
    {
        $portalId = PortalContextService::getCurrentPortalId();
        
        // Si no hay portal en contexto, no filtramos (superadmin ve todo)
        if (!$portalId) {
            return;
        }

        // Si el modelo tiene directamente portal_id
        if ($this->hasColumn($model, 'portal_id')) {
            $builder->where($model->getTable() . '.portal_id', $portalId);
        }
        // Si el modelo tiene empresa_id, filtramos a través de la empresa
        elseif ($this->hasColumn($model, 'empresa_id')) {
            $builder->whereHas('empresa', function ($query) use ($portalId) {
                $query->where('portal_id', $portalId);
            });
        }
    }

    /**
     * Check if model has a specific column
     */
    protected function hasColumn(Model $model, string $column): bool
    {
        return in_array($column, $model->getFillable()) 
            || $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), $column);
    }
}

<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Services\PortalContextService;

class PortalScope implements Scope
{
    /**
     * Cache for column checks to avoid repeated schema queries
     */
    protected static array $columnCache = [];

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

        $table = $model->getTable();

        // Si el modelo tiene directamente portal_id
        if ($this->hasColumn($model, $table, 'portal_id')) {
            $builder->where("{$table}.portal_id", $portalId);
        }
        // Si el modelo tiene empresa_id, filtramos a través de la empresa
        elseif ($this->hasColumn($model, $table, 'empresa_id')) {
            $builder->whereHas('empresa', function ($query) use ($portalId) {
                $query->withoutGlobalScope(self::class)->where('empresas.portal_id', $portalId);
            });
        }
    }

    /**
     * Check if model has a specific column (with caching)
     */
    protected function hasColumn(Model $model, string $table, string $column): bool
    {
        $cacheKey = "{$table}.{$column}";
        
        if (!isset(static::$columnCache[$cacheKey])) {
            // First check fillable (fast)
            if (in_array($column, $model->getFillable())) {
                static::$columnCache[$cacheKey] = true;
            } else {
                // Fallback to schema check (slower, but cached)
                try {
                    static::$columnCache[$cacheKey] = $model->getConnection()
                        ->getSchemaBuilder()
                        ->hasColumn($table, $column);
                } catch (\Exception $e) {
                    static::$columnCache[$cacheKey] = false;
                }
            }
        }
        
        return static::$columnCache[$cacheKey];
    }

    /**
     * Clear the column cache (useful for testing)
     */
    public static function clearColumnCache(): void
    {
        static::$columnCache = [];
    }
}

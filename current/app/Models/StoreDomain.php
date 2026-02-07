<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class StoreDomain extends Model
{
    protected $table = 'store_domains';
    protected $guarded = [];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
        'ssl_enabled' => 'boolean',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function store()
    {
        return $this->empresa();
    }

    public static function findByDomain(string $domain): ?self
    {
        $domain = strtolower(trim($domain));

        return Cache::remember("store_domain:{$domain}", 3600, function () use ($domain) {
            return self::where('domain', $domain)
                ->where('is_active', true)
                ->with('empresa')
                ->first();
        });
    }

    public static function clearCache(string $domain = null): void
    {
        if ($domain) {
            Cache::forget("store_domain:{$domain}");
        }
    }

    public function getFullUrlAttribute(): string
    {
        $protocol = $this->ssl_enabled ? 'https' : 'http';
        return "{$protocol}://{$this->domain}";
    }
}

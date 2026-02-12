<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Portal extends Model
{
    protected $table = 'portales';
    protected $guarded = [];

    protected $casts = [
        'settings' => 'array',
        'dominios' => 'array',
        'activo' => 'boolean',
        'flyer_enabled' => 'boolean',
        'show_prices_in_portal' => 'boolean',
        'ai_assistant_enabled' => 'boolean',
    ];

    public function empresas(): HasMany
    {
        return $this->hasMany(Empresa::class, 'portal_id');
    }

    public function configs(): HasMany
    {
        return $this->hasMany(PortalConfig::class, 'portal_id');
    }

    public function getLogoUrl(): ?string
    {
        if ($this->logo_path) {
            return Storage::disk('public')->url($this->logo_path);
        }
        return null;
    }

    public function getSetting(string $key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    public function getConfig(string $key, $default = null)
    {
        $config = PortalConfig::where('portal_id', $this->id)
            ->where('key', $key)
            ->first();
        return $config ? $config->value : $default;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresas';

    protected $fillable = [
        'nombre',
        'slug',
        'activa',
        'brand_nombre_publico',
        'brand_color',
        'logo_path',
        'skin',
        'config',
        'settings',
        'theme_id',
    ];

    protected $casts = [
        'config' => 'array',
        'settings' => 'array',
        'activa' => 'boolean',
    ];

    // Relationships
    public function categorias()
    {
        return $this->hasMany(Categoria::class, 'empresa_id');
    }

    public function productos()
    {
        return $this->hasMany(Producto::class, 'empresa_id');
    }

    public function ordenes()
    {
        return $this->hasMany(Orden::class, 'empresa_id');
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'empresa_id');
    }

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'empresa_usuario', 'empresa_id', 'usuario_id')
            ->withPivot('rol_id', 'activo')
            ->withTimestamps();
    }

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

    // Settings helpers
    public function getSetting(string $key, $default = null)
    {
        return data_get($this->settings, $key, $default);
    }

    public function setSetting(string $key, $value): self
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        $this->settings = $settings;
        return $this;
    }

    // Branding helpers
    public function getAppName(): string
    {
        return $this->getSetting('app_name')
            ?? $this->brand_nombre_publico
            ?? $this->nombre
            ?? config('app.name', 'EMC Abastos');
    }

    public function getLogoUrl(): ?string
    {
        if ($this->logo_path) {
            return asset('storage/' . $this->logo_path);
        }
        return $this->getSetting('logo_url') ?? asset('storage/brand/logo-iados.png');
    }

    public function getPrimaryColor(): string
    {
        return $this->getSetting('primary_color') ?? $this->brand_color ?? '#16a34a';
    }

    public function getSecondaryColor(): string
    {
        return $this->getSetting('secondary_color') ?? '#6b7280';
    }

    public function getAccentColor(): string
    {
        return $this->getSetting('accent_color') ?? '#3b82f6';
    }

    // MercadoPago helpers
    public function getMpAccessToken(): ?string
    {
        return $this->getSetting('mp_access_token');
    }

    public function getMpPublicKey(): ?string
    {
        return $this->getSetting('mp_public_key');
    }

    public function hasMercadoPago(): bool
    {
        return !empty($this->getMpAccessToken()) && !empty($this->getMpPublicKey());
    }

    // Catalog helpers
    public function getDefaultProductImage(): string
    {
        return $this->getSetting('default_product_image_url')
            ?? asset('images/producto-default.png');
    }
}

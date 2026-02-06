<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    protected $guarded = [];
    protected $casts = ['meta'=>'array','activo'=>'boolean'];

    public function categoria() { return $this->belongsTo(Categoria::class, 'categoria_id'); }
    public function empresa() { return $this->belongsTo(Empresa::class, 'empresa_id'); }

    public function getImagenUrlAttribute(): ?string
    {
        return data_get($this->meta,'imagen_url');
    }
}

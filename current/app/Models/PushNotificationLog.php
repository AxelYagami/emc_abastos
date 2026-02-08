<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushNotificationLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'payload_json' => 'array',
    ];

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'order_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }

    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }
}

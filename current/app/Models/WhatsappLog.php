<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToPortal;

class WhatsappLog extends Model
{
    use BelongsToPortal;

    protected $table = 'whatsapp_logs';
    protected $guarded = [];
    protected $casts = [
        'payload' => 'array',
        'provider_response' => 'array',
    ];

    /**
     * Relacion con la orden
     */
    public function orden(): BelongsTo
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }

    /**
     * Relacion con la empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}

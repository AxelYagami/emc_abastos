<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToPortal;

class Orden extends Model
{
    use BelongsToPortal;

    protected $table = 'ordenes';
    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
        'estimated_ready_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'preparing_at' => 'datetime',
        'ready_at' => 'datetime',
        'en_ruta_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(OrdenItem::class, 'orden_id');
    }

    public function pagos()
    {
        return $this->hasMany(OrdenPago::class, 'orden_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function repartidor()
    {
        return $this->belongsTo(Usuario::class, 'repartidor_id');
    }

    public function pushLogs()
    {
        return $this->hasMany(PushNotificationLog::class, 'order_id');
    }

    public function getTotal(): float
    {
        // Use 'total' column from orden if available, else calculate from items
        if ($this->total) {
            return (float) $this->total;
        }
        return $this->items->sum(fn($item) => $item->cantidad * $item->precio);
    }

    public function isPaid(): bool
    {
        return $this->pagos()->where('status', 'paid')->exists();
    }

    public function getPendingAmount(): float
    {
        $paid = $this->pagos()->where('status', 'paid')->sum('monto');
        return max(0, $this->getTotal() - $paid);
    }
}

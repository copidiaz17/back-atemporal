<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $producto_id
 * @property int $venta_id
 * @property int $venta_cantidad
 * @property float $venta_precio
 * @property float $venta_total
 * @property-read Producto $producto
 * @property-read Venta $venta
 */
class VentaDetalle extends Model
{
    use HasFactory;

    protected $table = 'venta_detalle';

    protected $fillable = [
        'producto_id',
        'venta_id',
        'venta_cantidad',
        'venta_precio',
        'venta_total',
    ];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function (VentaDetalle $detalle) {
            $detalle->venta_total = $detalle->venta_precio * $detalle->venta_cantidad;
        });

        // Descontar stock al crear un detalle
        static::created(function (VentaDetalle $detalle) {
            $producto = $detalle->producto;
            $producto->producto_cantidad -= $detalle->venta_cantidad;
            $producto->save();
        });

        // Revertir stock al eliminar un detalle
        static::deleted(function (VentaDetalle $detalle) {
            $producto = $detalle->producto;
            $producto->producto_cantidad += $detalle->venta_cantidad;
            $producto->save();
        });

        static::updated(function (VentaDetalle $detalle) {
            $producto = $detalle->producto;
            $cantidadOriginal = $detalle->getOriginal('venta_cantidad');
            $nuevaCantidad = $detalle->venta_cantidad;
            $diferencia = $cantidadOriginal - $nuevaCantidad;

            // Ajustar el stock según la diferencia
            $producto->producto_cantidad += $diferencia;
            $producto->save();
        });
    }
}

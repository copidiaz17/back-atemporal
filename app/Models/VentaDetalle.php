<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function getCalculatedTotalAttribute()
    {
        return $this->venta_precio * $this->venta_cantidad;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($detalle) {
            $detalle->venta_total = $detalle->venta_precio * $detalle->venta_cantidad;
        });

        // Antes de crear un detalle: validar que hay stock suficiente
        // static::creating(function ($detalle) {
        //     $producto = $detalle->producto;
        //     if ($producto->stock && $detalle->venta_cantidad > $producto->stock->cantidad) {
        //         throw new \Exception("No hay suficiente stock para el producto: {$producto->producto_nombre}.");
        //     }
        // });

        // Descontar stock al crear un detalle
        static::created(function ($detalle) {
            $producto = $detalle->producto;
            if ($producto->stock) {
                $producto->stock->cantidad -= $detalle->venta_cantidad;
                $producto->stock->save();
            }
        });

        // Revertir stock al eliminar un detalle
        static::deleted(function ($detalle) {
            $producto = $detalle->producto;
            if ($producto->stock) {
                $producto->stock->cantidad += $detalle->venta_cantidad;
                $producto->stock->save();
            }
        });

        // Validar y ajustar stock al modificar un detalle
        static::updating(function ($detalle) {
            $producto = $detalle->producto;
            if ($producto->stock) {
                $cantidadOriginal = $detalle->getOriginal('venta_cantidad');
                $nuevaCantidad = $detalle->venta_cantidad;
                $diferencia = $nuevaCantidad - $cantidadOriginal;

                // Verificar si el stock actual permite la actualización
                // if ($diferencia > 0 && $diferencia > $producto->stock->cantidad) {
                //     throw new \Exception("No hay suficiente stock para actualizar la venta del producto: {$producto->producto_nombre}.");
                // }
            }
        });

        static::updated(function ($detalle) {
            $producto = $detalle->producto;
            if ($producto->stock) {
                $cantidadOriginal = $detalle->getOriginal('venta_cantidad');
                $nuevaCantidad = $detalle->venta_cantidad;
                $diferencia = $cantidadOriginal - $nuevaCantidad;

                // Ajustar el stock según la diferencia
                $producto->stock->cantidad += $diferencia;
                $producto->stock->save();
            }
        });
    }
}

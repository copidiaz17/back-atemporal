<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ventadetalle extends Model
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

    // Relación con la venta
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id', 'id');
    }

    // Relación con el producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id'  );
    }

    // Accessor para calcular el total del detalle (cantidad * precio)
    public function getCalculatedTotalAttribute()
    {
        return $this->venta_precio * $this->venta_cantidad;
    }

    // Evento para actualizar el total antes de guardar
    protected static function booted()
    {
        static::saving(function ($detalle) {
            $detalle->venta_total = $detalle->venta_precio * $detalle->venta_cantidad;
        });
    }

    protected static function boot()
    {
        
        parent::boot();

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

        // Actualizar stock al modificar un detalle
        static::updated(function ($detalle) {
            $producto = $detalle->producto;
            if ($producto->stock) {
                // Ajusta el stock en base al cambio en la cantidad
                $diferencia = $detalle->getOriginal('venta_cantidad') - $detalle->venta_cantidad;
                $producto->stock->cantidad += $diferencia;
                $producto->stock->save();
            }
        });
    }
}
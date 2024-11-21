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
        return $this->belongsTo(Producto::class, 'producto_id');
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
}
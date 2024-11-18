<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ventadetalle extends Model
{
    use HasFactory;

    protected $table = 'venta_detalle';

    protected $fillable = [
        'venta_id', 
        'producto_id', 
        'venta_cantidad', 
        'venta_precio', 
        'venta_total',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function getVentaTotalAttribute()
    {
        return $this->venta_precio * $this->venta_cantidad;
    }

    public function setVentaTotalAttribute($value)
    {
        $this->attributes['venta_total'] = $this->venta_precio * $this->venta_cantidad;
    }
}

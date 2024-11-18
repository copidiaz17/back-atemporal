<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ventadetalle extends Model
{
    use HasFactory;

    protected $table = 'venta_detalle';

    protected $fillable = [
<<<<<<< HEAD
        'venta_id', 
        'producto_id', 
        'venta_cantidad', 
        'venta_precio', 
        'venta_total',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
=======
        'producto_id',
        'venta_id',
        'venta_cantidad',
        'venta_precio',
        'venta_total'
    ];

        

    public function venta() {
        return $this->belongsTo(Venta::class, 'venta_id', 'id');
>>>>>>> 8375618a4e22c8de21d81411ddce9638bc7268f5
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

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

    public function Producto()
{
    return $this->belongsTo(Producto::class);
}

public function venta()
{
    return $this->belongsTo(Venta::class);
}

}

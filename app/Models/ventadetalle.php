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
<<<<<<< HEAD
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

=======
        'venta_cantidad',
        'venta_id',
        'producto_id'
    ];

    public function venta() {
        return $this->belongsTo(Venta::class, 'venta_id', 'id');
    }

    public function producto() {
        return $this->belongsTo(Producto::class, 'producto_id', 'id');
    }
>>>>>>> 6b97c3c25996e538f427cc68bef3432522ae4440
}

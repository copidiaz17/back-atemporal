<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class venta extends Model
{
    use HasFactory;

    protected $table = 'venta';
    protected $primaryKey = 'id';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'cliente_id',
        'venta_fecha',
    ];

    // Relación con el modelo User
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id', 'id');
    }

    // Relación con detalles de la venta
    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class, 'venta_id', 'id');
    }

    // Accessor para calcular el monto total de la venta
    public function getTotalAttribute()
    {
        $this->loadMissing('detalles'); // Asegura que los detalles estén cargados
        return $this->detalles->sum(fn($detalle) => $detalle->venta_total ?? 0);
    }

    protected static function booted()
{
    static::created(function ($ventaDetalle) {
        $producto = Producto::find($ventaDetalle->producto_id);

        if ($producto) {
            $nuevoStock = $producto->producto_cantidad - $ventaDetalle->venta_cantidad;

            if ($nuevoStock < 0) {
                throw new \Exception("No hay suficiente stock disponible para el producto: {$producto->producto_nombre}");
            }

            $producto->update(['producto_cantidad' => $nuevoStock]);
        }
    });
}

}

<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $producto_nombre
 * @property string $producto_descripcion
 * @property string $producto_imagen
 * @property float $producto_precio
 * @property int $categoria_id
 * @property int $producto_cantidad
 * @property-read Categoria $categoria
 * @property-read Collection<VentaDetalle> $ventas
 */
class Producto extends Model
{
    use HasFactory;

    protected $table = 'producto';

    protected $fillable = [
        'producto_nombre',
        'producto_descripcion',
        'producto_imagen',
        'producto_precio',
        'producto_cantidad',
        'categoria_id',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(VentaDetalle::class);
    }
}

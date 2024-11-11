<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory;

    protected $table = "productos";

    protected $fillable = [
        'producto_nombre',
        'producto_descripcion',
        'producto_imagen',
        'producto_precio',
        'categoria_id',

    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(DetalleVenta::class, 'producto_id', 'id');
    }
}

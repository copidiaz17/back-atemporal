<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'producto';

    protected $fillable = [
        'producto_nombre',
        'producto_descripcion',
        'producto_imagen',
        'producto_precio',
        'categoria_id',
        'producto_cantidad',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class);
    }
}

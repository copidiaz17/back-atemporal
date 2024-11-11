<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'ventas';

    protected $fillable  = [
        'cliente_id',
        'venta_fecha'
    ];


    public function cliente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cliente_id', 'id');
    }

    public function detalle(): HasMany
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id', 'id');
    }
}

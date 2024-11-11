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

    protected $fillable  = [
        'cliente_id',
        'venta_fecha'
    ];


    public function cliente() {
        return $this->belongsTo(User::class, 'cliente_id', 'id');
    }

    public function detalle() {
        return $this->hasMany(VentaDetalle::class, 'venta_id', 'id');
    }

}

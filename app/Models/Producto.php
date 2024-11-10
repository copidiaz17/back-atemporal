<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;
    protected $table = "producto";
    protected $primaryKey = 'id';

    public $incrementing = true; 
    protected $keyType = 'int'; 
    public function categoria(){
      return $this->belongsTo(Categoria::class, 'categoria_id');
}

    protected $fillable = [
        'producto_nombre',
        'producto_descripcion',
        'producto_imagen', 
        'producto_precio',
        'categoria_id',

    ];


    public function ventas() {
        $this->hasMany(VentaDetalle::class, 'producto_id', 'id');
    }


    
}

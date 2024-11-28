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
    

    protected $fillable = [
        'producto_nombre',
        'producto_descripcion',
        'producto_imagen', 
        'producto_precio',
        'categoria_id',
        'producto_cantidad',

        

    ];


    public function categoria(){ 
        return $this->belongsTo(Categoria::class, 'categoria_id'); 
    } 
    public function ventas() { 
        return $this->hasMany(VentaDetalle::class, 'producto_id', 'id'); 
    }
     public function stock() { 
        return $this->hasOne(Stock::class, 'producto_id', 'id'); 
    }

    
}

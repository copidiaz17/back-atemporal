<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $table = "stock";

    protected $fillable = [
        'cantidad',
        'producto_id'
    ];

    public function producto() {
        return $this->belongsTo(Producto::class, 'producto_id', 'id');
    }
}

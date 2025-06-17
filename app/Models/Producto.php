<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos'; 

    protected $fillable = [
        'titulo',
        'descripcion',
        'precio',
        'imagen',
        'stock', 
    ];

    /**
     * The categories that belong to the product.
     * Define la relación muchos a muchos con categorías, especificando la tabla pivote.
     */
    public function categorias()
    {
        // El segundo argumento es el nombre de la tabla pivote.
        return $this->belongsToMany(Categoria::class, 'categoria_productos', 'producto_id', 'categoria_id');
    }

    /**
     * The orders that the product belongs to.
     * Define la relación muchos a muchos con pedidos a través de la tabla pivote.
     */
    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class, 'pedido_producto', 'producto_id', 'pedido_id')
                    ->withPivot('cantidad', 'precio_unitario', 'subtotal'); 
    }
}
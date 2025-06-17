<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedidos extends Model
{
    use HasFactory;

    // Asegúrate de que el nombre de la tabla sea correcto si es plural (por ejemplo, 'pedidos')
    protected $table = 'pedidos'; 

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'usuario_id',
        'direccion_id',
        'total',
        'estado', // <--- ¡ASEGÚRATE DE QUE 'estado' ESTÉ AQUÍ!
        'fecha_pedido',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Get the shipping address for the order.
     */
    public function direccion() // Asumiendo que tu modelo de direcciones se llama 'Direcciones'
    {
        return $this->belongsTo(Direcciones::class, 'direccion_id');
    }

    /**
     * The products that belong to the order.
     */
    public function productos()
    {
        // Asumiendo que tu tabla pivote se llama 'pedido_producto'
        // y tus claves foráneas son 'pedido_id' y 'producto_id'
        return $this->belongsToMany(Producto::class, 'pedido_producto', 'pedido_id', 'producto_id')
                    ->withPivot('cantidad', 'precio_unitario', 'subtotal');
    }
}
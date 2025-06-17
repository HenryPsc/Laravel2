<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cart_items'; // Asegura que el modelo apunte a la tabla correcta

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
    ];

    /**
     * Get the user that owns the cart item.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product associated with the cart item.
     */
    public function producto() // Asegúrate de que el nombre del método coincida con tu modelo de producto
    {
        return $this->belongsTo(Producto::class, 'product_id'); // Asegura que se usa 'product_id'
    }
}
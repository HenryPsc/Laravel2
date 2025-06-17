<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id(); // ID único para cada ítem del carrito

            // Clave foránea para el usuario que posee el carrito
            // onDelete('cascade') significa que si el usuario es eliminado, sus ítems del carrito también lo serán.
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Clave foránea para el producto en el carrito
            // onDelete('cascade') significa que si el producto es eliminado, se quita del carrito.
            $table->foreignId('product_id')->constrained('productos')->onDelete('cascade');

            $table->integer('quantity')->default(1); // Cantidad del producto en el carrito

            // Asegura que un usuario solo pueda tener un producto específico una vez en el carrito
            $table->unique(['user_id', 'product_id']); 
            
            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
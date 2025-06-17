<?php

namespace Database\Seeders;

// Asegúrate de que todos tus seeders estén importados aquí
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder; // Si tienes un UserSeeder
use Database\Seeders\CategoriaSeeder; // Importa CategoriaSeeder
use Database\Seeders\ProductoSeeder; // Importa ProductoSeeder
use Database\Seeders\CategoriaProductoSeeder; // Tu seeder específico
use Database\Seeders\UsuarioDireccionSeeder; // El seeder que me mostraste antes y que crea usuarios y direcciones.

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Llamar a los seeders en el orden correcto de dependencia
        $this->call([
            // 1. Primero crea usuarios (ya que Direcciones y Pedidos dependen de ellos)
            UsuarioDireccionSeeder::class, // Usa este si es el que crea tus usuarios

            // 2. Luego, categorías y productos (ya que categoria_producto depende de ellos)
            CategoriaSeeder::class,  // <<-- DESCOMENTAR Y AÑADIR ESTE
            ProductoSeeder::class,   // <<-- DESCOMENTAR Y AÑADIR ESTE
            
            // 3. Finalmente, el seeder que relaciona categorías y productos
            CategoriaProductoSeeder::class, 

            // Si tienes otros seeders (como para carritos, pedidos iniciales, etc.)
            // irían después de que todas sus dependencias estén creadas.
        ]);
    }
}
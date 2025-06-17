<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto; // Asegúrate de importar tu modelo Producto

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Producto::create([
            'titulo' => 'Laptop X1',
            'descripcion' => 'Laptop de alto rendimiento',
            'precio' => 1200.50,
            'stock' => 10,
            'imagen' => 'https://firebasestorage.googleapis.com/v0/b/pruebaimg-f6ce6.firebasestorage.app/o/APLIWEB2%2FLaptop.jpg?alt=media&token=8c73c022-4fe3-4591-8182-dcb5bb0083ab',
        ]);
        Producto::create([
            'titulo' => 'Camisa Casual',
            'descripcion' => 'Camisa de algodón',
            'precio' => 25.99,
            'stock' => 50,
            'imagen' => 'https://firebasestorage.googleapis.com/v0/b/pruebaimg-f6ce6.firebasestorage.app/o/APLIWEB2%2F41VLZloiAFL._AC_.jpg?alt=media&token=e27211f2-3a42-47d3-9bb9-5f6fd727d4f7',
        ]);
        Producto::create([
            'titulo' => 'Sofá Moderno',
            'descripcion' => 'Sofá de diseño cómodo',
            'precio' => 499.99,
            'stock' => 5,
            'imagen' => 'https://firebasestorage.googleapis.com/v0/b/pruebaimg-f6ce6.firebasestorage.app/o/APLIWEB2%2Fimages.jpg?alt=media&token=b3c39672-a154-4eeb-8da3-a793774670d5',
        ]);
        // Puedes añadir más productos si lo deseas
    }
}
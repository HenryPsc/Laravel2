<?php
 
namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\Producto;
 
class CategoriaProductoSeeder extends Seeder
{
    public function run(): void
    {
        // Crear categorías usando firstOrCreate
        $categorias = [
            ['nombre' => 'Electrónica', 'slug' => 'electronica'],
            ['nombre' => 'Ropa', 'slug' => 'ropa'],
            ['nombre' => 'Hogar', 'slug' => 'hogar'],
        ];
 
        foreach ($categorias as $catData) {
            Categoria::firstOrCreate(
                ['slug' => $catData['slug']],
                $catData
            );
        }
 
        // Crear productos
        $productos = [
            [
                'titulo' => 'Laptop X1',
                'descripcion' => 'Laptop de alto rendimiento',
                'precio' => 1200.50,
                'imagen' => 'https://firebasestorage.googleapis.com/v0/b/pruebaimg-f6ce6.firebasestorage.app/o/laptop.jpg?alt=media&token=2fd049a8-5562-42ec-8689-b0bebca5ce20',
                'stock' => 10,
            ],
            [
                'titulo' => 'Camisa Casual',
                'descripcion' => 'Camisa de algodón',
                'precio' => 25.99,
                'imagen' => 'https://firebasestorage.googleapis.com/v0/b/pruebaimg-f6ce6.firebasestorage.app/o/16695155-0_product_1200Wx1800H.webp?alt=media&token=5cd861a5-5a7f-4179-a5e1-b50c723617a1',
                'stock' => 50,
            ],
            [
                'titulo' => 'Sofá Moderno',
                'descripcion' => 'Sofá de diseño cómodo',
                'precio' => 499.99,
                'imagen' => 'https://firebasestorage.googleapis.com/v0/b/pruebaimg-f6ce6.firebasestorage.app/o/SARA-CON-BASES-2.jpg?alt=media&token=5fa19109-84b7-4f5d-9221-974af346d314',
                'stock' => 5,
            ],
        ];
 
        foreach ($productos as $prodData) {
            $producto = Producto::firstOrCreate(
                ['titulo' => $prodData['titulo']],
                $prodData
            );
 
            // Asignar categorías aleatorias
            $categoriaIds = Categoria::inRandomOrder()->limit(rand(1, 2))->pluck('id')->toArray();
            $producto->categorias()->sync($categoriaIds);
        }
    }
}
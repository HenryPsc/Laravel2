<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria; // Asegúrate de importar el modelo Categoria
use App\Models\Producto;  // Asegúrate de importar el modelo Producto

class CategoriaProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // NO CREAR CATEGORÍAS AQUÍ - DEBEN SER CREADAS POR CategoriaSeeder
        // NO CREAR PRODUCTOS AQUÍ - DEBEN SER CREADOS POR ProductoSeeder

        // Lógica para asignar categorías a productos existentes
        // Esto asume que ProductoSeeder y CategoriaSeeder ya se ejecutaron.

        // Ejemplo: Asignar categorías a la Laptop X1
        $laptop = Producto::where('titulo', 'Laptop X1')->first();
        if ($laptop) {
            $electronica = Categoria::where('nombre', 'Electrónica')->first();
            if ($electronica) {
                $laptop->categorias()->syncWithoutDetaching([$electronica->id]);
            }
        }

        // Ejemplo: Asignar categorías a la Camisa Casual
        $camisa = Producto::where('titulo', 'Camisa Casual')->first();
        if ($camisa) {
            $ropa = Categoria::where('nombre', 'Ropa')->first();
            if ($ropa) {
                $camisa->categorias()->syncWithoutDetaching([$ropa->id]);
            }
        }

        // Ejemplo: Asignar categorías al Sofá Moderno
        $sofa = Producto::where('titulo', 'Sofá Moderno')->first();
        if ($sofa) {
            $hogar = Categoria::where('nombre', 'Hogar')->first();
            if ($hogar) {
                $sofa->categorias()->syncWithoutDetaching([$hogar->id]);
            }
        }

        // Puedes automatizar esto aún más si tienes muchos productos y categorías
        // Por ejemplo, iterar sobre todos los productos y asignar categorías aleatorias o predefinidas
        // Ejemplo de asignación automática de categorías aleatorias (para todos los productos):
        $productos = Producto::all();
        foreach ($productos as $producto) {
            // Asignar categorías aleatorias a cada producto
            $categoriaIds = Categoria::inRandomOrder()->limit(rand(1, 2))->pluck('id')->toArray();
            $producto->categorias()->syncWithoutDetaching($categoriaIds);
        }
    }
}
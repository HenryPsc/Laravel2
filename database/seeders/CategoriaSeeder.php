<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria; // Asegúrate de importar tu modelo Categoria
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('categorias')->truncate();
        Schema::enableForeignKeyConstraints();

        // Añadir el campo 'slug' a cada categoría
        Categoria::create(['nombre' => 'Electrónica', 'slug' => 'electronica']);
        Categoria::create(['nombre' => 'Ropa', 'slug' => 'ropa']);
        Categoria::create(['nombre' => 'Hogar', 'slug' => 'hogar']);
        // Puedes añadir más categorías si lo deseas
    }
}
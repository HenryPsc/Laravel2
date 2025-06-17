<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Validation\Rule; 
use Illuminate\Support\Facades\Auth; 

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with('categorias')->orderBy('created_at', 'desc')->get();
        return response()->json($productos);
    }

    public function show(Producto $producto)
    {
        return response()->json($producto->load('categorias'));
    }

    public function store(Request $request)
    {
        if (Auth::check() && Auth::user()->rol === 'admin') {
            $validatedData = $request->validate([
                'titulo' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'precio' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'imagen' => 'nullable|string', 
                'categorias' => 'array', 
                'categorias.*' => 'exists:categorias,id',
                'status' => ['required', 'string', Rule::in(['activo', 'inactivo', 'agotado', 'destacado'])],
            ]);

            try {
                $producto = Producto::create($validatedData);
                
                if (isset($validatedData['categorias'])) {
                    $producto->categorias()->sync($validatedData['categorias']);
                }

                return response()->json(['message' => 'Producto creado exitosamente.', 'producto' => $producto->load('categorias')], 201);
            } catch (\Exception $e) {
                \Log::error('Error al crear el producto: ' . $e->getMessage(), ['exception' => $e, 'request_data' => $request->all()]);
                return response()->json(['message' => 'Error al crear el producto.', 'error' => $e->getMessage()], 500);
            }
        } else {
            return response()->json(['message' => 'Acceso denegado: solo administradores pueden crear productos.'], 403);
        }
    }

    public function update(Request $request, Producto $producto)
    {
        if (Auth::check() && Auth::user()->rol === 'admin') {
            $validatedData = $request->validate([
                'titulo' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'precio' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'imagen' => 'nullable|string', 
                'categorias' => 'array',
                'categorias.*' => 'exists:categorias,id',
                'status' => ['required', 'string', Rule::in(['activo', 'inactivo', 'agotado', 'destacado'])],
            ]);

            try {
                $producto->update($validatedData);

                if (isset($validatedData['categorias'])) {
                    $producto->categorias()->sync($validatedData['categorias']);
                } else {
                    $producto->categorias()->detach(); 
                }

                return response()->json(['message' => 'Producto actualizado exitosamente.', 'producto' => $producto->load('categorias')], 200);
            } catch (\Exception $e) {
                \Log::error('Error al actualizar el producto: ' . $e->getMessage(), ['exception' => $e, 'request_data' => $request->all()]);
                return response()->json(['message' => 'Error al actualizar el producto.', 'error' => $e->getMessage()], 500);
            }
        } else {
            return response()->json(['message' => 'Acceso denegado: solo administradores pueden actualizar productos.'], 403);
        }
    }

    public function destroy(Producto $producto)
    {
        if (Auth::check() && Auth::user()->rol === 'admin') {
            try {
                $producto->categorias()->detach();
                $producto->delete();
                return response()->json(['message' => 'Producto eliminado exitosamente.'], 200);
            } catch (\Exception $e) {
                \Log::error('Error al eliminar el producto: ' . $e->getMessage(), ['exception' => $e]);
                return response()->json(['message' => 'Error al eliminar el producto.', 'error' => $e->getMessage()], 500);
            }
        } else {
            return response()->json(['message' => 'Acceso denegado: solo administradores pueden eliminar productos.'], 403);
        }
    }

    public function updateProductStatus(Request $request, Producto $producto)
    {
        if (Auth::check() && Auth::user()->rol === 'admin') {
            $validatedData = $request->validate([
                'status' => ['required', 'string', Rule::in(['activo', 'inactivo', 'agotado', 'destacado'])],
            ]);

            try {
                $producto->status = $validatedData['status'];
                $producto->save();

                return response()->json(['message' => 'Estado del producto actualizado exitosamente.', 'producto' => $producto], 200);
            } catch (\Exception $e) {
                \Log::error('Error al actualizar el estado del producto: ' . $e->getMessage(), ['exception' => $e]);
                return response()->json(['message' => 'Error al actualizar el estado del producto.', 'error' => $e->getMessage()], 500);
            }
        } else {
            return response()->json(['message' => 'Acceso denegado. Solo administradores pueden cambiar el estado de los productos.'], 403);
        }
    }
}
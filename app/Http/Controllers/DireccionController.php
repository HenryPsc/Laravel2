<?php

namespace App\Http\Controllers;

use App\Models\Direcciones; // Importar el modelo Direcciones (con 's' al final, como tu lo tienes)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Para acceder al usuario autenticado
use Illuminate\Support\Facades\Validator; // Para validación manual si es necesario

class DireccionController extends Controller
{
    /**
     * Display a listing of the user's addresses.
     * Muestra una lista de las direcciones del usuario autenticado.
     */
    public function index()
    {
        $userId = Auth::id(); // Obtener el ID del usuario autenticado
        $direcciones = Direcciones::where('usuario_id', $userId)->get(); // Usar 'usuario_id' como en tu tabla

        return response()->json($direcciones);
    }

    /**
     * Store a newly created address in storage.
     * Almacena una nueva dirección en la base de datos para el usuario autenticado.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:255',
            'provincia' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            // Asegúrate de que estos campos coincidan con tus columnas reales en la tabla 'direcciones'
        ]);

        $direccion = Direcciones::create([
            'usuario_id' => Auth::id(), // Asocia la dirección al usuario autenticado
            'direccion' => $validatedData['direccion'],
            'ciudad' => $validatedData['ciudad'],
            'provincia' => $validatedData['provincia'],
            'telefono' => $validatedData['telefono'],
        ]);

        return response()->json([
            'message' => 'Dirección creada exitosamente.',
            'direccion' => $direccion
        ], 201);
    }

    /**
     * Display the specified address.
     * Muestra una dirección específica del usuario autenticado.
     */
    public function show(Direcciones $direccion) // Usamos Route Model Binding para Direcciones
    {
        // Asegurarse de que la dirección pertenece al usuario autenticado
        if ($direccion->usuario_id !== Auth::id()) {
            return response()->json(['message' => 'Acceso denegado. Esta dirección no te pertenece.'], 403);
        }

        return response()->json($direccion);
    }

    /**
     * Update the specified address in storage.
     * Actualiza una dirección específica del usuario autenticado.
     */
    public function update(Request $request, Direcciones $direccion) // Usamos Route Model Binding para Direcciones
    {
        // Asegurarse de que la dirección pertenece al usuario autenticado
        if ($direccion->usuario_id !== Auth::id()) {
            return response()->json(['message' => 'Acceso denegado. Esta dirección no te pertenece.'], 403);
        }

        $validatedData = $request->validate([
            'direccion' => 'sometimes|required|string|max:255',
            'ciudad' => 'sometimes|required|string|max:255',
            'provincia' => 'sometimes|required|string|max:255',
            'telefono' => 'sometimes|required|string|max:20',
        ]);

        $direccion->update($validatedData);

        return response()->json([
            'message' => 'Dirección actualizada exitosamente.',
            'direccion' => $direccion
        ], 200);
    }

    /**
     * Remove the specified address from storage.
     * Elimina una dirección específica del usuario autenticado.
     */
    public function destroy(Direcciones $direccion) // Usamos Route Model Binding para Direcciones
    {
        // Asegurarse de que la dirección pertenece al usuario autenticado
        if ($direccion->usuario_id !== Auth::id()) {
            return response()->json(['message' => 'Acceso denegado. Esta dirección no te pertenece.'], 403);
        }

        $direccion->delete();

        return response()->json(['message' => 'Dirección eliminada exitosamente.'], 200);
    }
}
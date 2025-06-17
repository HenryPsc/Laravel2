<?php

namespace App\Http\Controllers;

use App\Models\CartItem; // Importar el modelo CartItem
use App\Models\Producto; // Importar el modelo Producto
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Para acceder al usuario autenticado
use Illuminate\Support\Facades\DB; // Para transacciones si se necesitan

class CarritoController extends Controller
{
    /**
     * Display a listing of the user's cart items.
     * Muestra una lista de los ítems del carrito del usuario autenticado.
     */
    public function index()
    {
        // Obtener el ID del usuario autenticado
        $userId = Auth::id();

        // Cargar los ítems del carrito para este usuario, con los detalles del producto
        $cartItems = CartItem::with('producto') // Cargar la relación con el producto
                            ->where('user_id', $userId)
                            ->get();

        // Calcular el total del carrito
        $total = $cartItems->sum(function($item) {
            // Asegúrate de que item->producto y item->producto->precio existen
            return $item->quantity * ($item->producto ? $item->producto->precio : 0);
        });

        // Retornar los ítems del carrito y el total en formato JSON
        return response()->json([
            'items' => $cartItems,
            'total' => round($total, 2) // Redondear el total a 2 decimales
        ]);
    }

    /**
     * Add a product to the cart or update its quantity.
     * Añade un producto al carrito o actualiza su cantidad si ya existe.
     */
    public function add(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'product_id' => 'required|exists:productos,id',
                'quantity' => 'sometimes|integer|min:1', // <<-- LA CORRECCIÓN ESTÁ AQUÍ: Se eliminó "|default:1"
            ]);

            $userId = Auth::id();
            $productId = $validatedData['product_id'];
            $quantity = $validatedData['quantity'] ?? 1; // Correctamente asignado aquí

            // Buscar si el producto ya existe en el carrito del usuario
            $cartItem = CartItem::where('user_id', $userId)
                                ->where('product_id', $productId)
                                ->first();

            if ($cartItem) {
                // Si el producto ya existe, incrementar la cantidad
                $cartItem->quantity += $quantity;
                $cartItem->save();
                $message = 'Cantidad de producto actualizada en el carrito.';
            } else {
                // Si el producto no existe, crearlo como un nuevo ítem en el carrito
                $cartItem = CartItem::create([
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
                $message = 'Producto añadido al carrito.';
            }

            // Cargar los detalles del producto para la respuesta
            $cartItem->load('producto');

            return response()->json([
                'message' => $message,
                'cart_item' => $cartItem
            ], 200);

        } catch (\Exception $e) {
            return back()->with('error', 'No se pudo añadir el producto al carrito: ' . $e->getMessage());
        }
    }

    /**
     * Update the quantity of a specific cart item.
     * Actualiza la cantidad de un ítem específico en el carrito.
     */
    public function updateQuantity(Request $request, Producto $producto) // Usamos Route Model Binding para Producto
    {
        $validatedData = $request->validate([
            'quantity' => 'required|integer|min:1', // La nueva cantidad debe ser al menos 1
        ]);

        $userId = Auth::id();

        try {
            // Buscar el ítem del carrito para el usuario y el producto dado
            $cartItem = CartItem::where('user_id', $userId)
                                ->where('product_id', $producto->id)
                                ->firstOrFail(); // Si no lo encuentra, lanza un 404

            $cartItem->quantity = $validatedData['quantity'];
            $cartItem->save();

            $cartItem->load('producto'); // Cargar los detalles del producto para la respuesta

            return response()->json([
                'message' => 'Cantidad de producto actualizada exitosamente.',
                'cart_item' => $cartItem
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Producto no encontrado en el carrito.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar la cantidad del producto.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove a product from the cart.
     * Elimina un producto del carrito.
     */
    public function remove(Producto $producto) // Usamos Route Model Binding para Producto
    {
        $userId = Auth::id();

        try {
            // Buscar y eliminar el ítem del carrito para el usuario y el producto dado
            $deleted = CartItem::where('user_id', $userId)
                                ->where('product_id', $producto->id)
                                ->delete();

            if ($deleted) {
                return response()->json(['message' => 'Producto eliminado del carrito exitosamente.'], 200);
            } else {
                return response()->json(['message' => 'Producto no encontrado en el carrito o no pudo ser eliminado.'], 404);
            }

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el producto del carrito.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Clear all items from the user's cart.
     * Vacía todos los ítems del carrito del usuario.
     */
    public function clear()
    {
        $userId = Auth::id();

        try {
            CartItem::where('user_id', $userId)->delete();
            return response()->json(['message' => 'Carrito vaciado exitosamente.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al vaciar el carrito.', 'error' => $e->getMessage()], 500);
        }
    }
}
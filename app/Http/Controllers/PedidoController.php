<?php

namespace App\Http\Controllers;

use App\Models\Pedidos; // <--- ASEGÚRATE DE QUE ESTO ES 'Pedidos' (con 's')
use App\Models\CartItem;
use App\Models\Direcciones; // <--- Y ESTO ES 'Direcciones' (con 's')
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PedidoController extends Controller
{
    /**
     * Process the checkout, creating an order from the user's cart.
     * Procesa la compra, creando un pedido a partir del carrito del usuario.
     */
    public function checkout(Request $request)
    {
        $validatedData = $request->validate([
            'direccion_id' => 'required|exists:direcciones,id', // ID de la dirección seleccionada
        ]);

        $userId = Auth::id();
        $direccionId = $validatedData['direccion_id'];

        $cartItems = CartItem::with('producto')
                            ->where('user_id', $userId)
                            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Tu carrito está vacío.'], 400);
        }

        $direccion = Direcciones::where('id', $direccionId) // <<--- Usando 'Direcciones' aquí
                                ->where('usuario_id', $userId)
                                ->first();

        if (!$direccion) {
            return response()->json(['message' => 'La dirección seleccionada no es válida o no te pertenece.'], 403);
        }

        DB::beginTransaction();
        try {
            $totalPedido = 0;
            $pedidoProductos = [];

            foreach ($cartItems as $cartItem) {
                $producto = $cartItem->producto;

                if ($producto->stock < $cartItem->quantity) {
                    DB::rollBack();
                    return response()->json(['message' => "Stock insuficiente para {$producto->titulo}. Stock disponible: {$producto->stock}"], 400);
                }

                $subtotalItem = $cartItem->quantity * $producto->precio;
                $totalPedido += $subtotalItem;

                $pedidoProductos[$producto->id] = [
                    'cantidad' => $cartItem->quantity,
                    'precio_unitario' => $producto->precio,
                    'subtotal' => $subtotalItem
                ];

                $producto->stock -= $cartItem->quantity;
                $producto->save();
            }

            // Crear el nuevo pedido usando el modelo 'Pedidos'
            $pedido = Pedidos::create([ // <<--- Usando 'Pedidos' aquí
                'usuario_id' => $userId,
                'direccion_id' => $direccionId,
                'total' => round($totalPedido, 2),
                'estado' => 'pendiente',
                'fecha_pedido' => now(),
            ]);

            $pedido->productos()->attach($pedidoProductos);

            CartItem::where('user_id', $userId)->delete();

            DB::commit();

            return response()->json([
                'message' => 'Pedido realizado exitosamente.',
                'pedido' => $pedido->load('productos', 'direccion')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al procesar el pedido: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'Error al procesar el pedido.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display a listing of the user's orders.
     * Muestra una lista de los pedidos del usuario autenticado.
     */
    
    public function index()
    {
        $userId = Auth::id();
        $pedidos = Pedidos::with('direccion', 'productos')
                            ->where('usuario_id', $userId)
                            ->orderBy('created_at', 'desc')
                            ->get();

        return response()->json($pedidos);
    }

    /**
     * Display the specified order.
     * Muestra un pedido específico del usuario autenticado.
     */
public function show(Pedidos $pedido)
    {
        // Verificar si el usuario es un administrador
        if (Auth::check() && Auth::user()->rol === 'admin') {
            // Un administrador puede ver cualquier pedido, no se necesita validación de usuario_id
            $pedido->load('direccion', 'productos', 'user'); 
            return response()->json($pedido);
        }
        // Si no es administrador, solo puede ver sus propios pedidos
        else if (Auth::check() && $pedido->usuario_id === Auth::id()) {
            $pedido->load('direccion', 'productos', 'user'); 
            return response()->json($pedido);
        }
        // Si no está autenticado o no tiene permisos
        else {
            return response()->json(['message' => 'Acceso denegado. No tienes permisos para ver este pedido.'], 403);
        }
    }

    /**
     * Update the status of a specific order.
     * Actualiza el estado de un pedido específico (para uso de administrador).
     */
    public function updateStatus(Request $request, Pedidos $pedido) // <<--- Usando 'Pedidos' aquí
    {
        if (Auth::check() && Auth::user()->rol === 'admin') {
            $validatedData = $request->validate([
                'estado' => ['required', 'string', Rule::in(['pendiente', 'procesando', 'enviado', 'completado', 'cancelado'])],
            ]);

            try {
                $pedido->estado = $validatedData['estado'];
                $pedido->save();

                $pedido->load('direccion', 'productos', 'user');

                return response()->json([
                    'message' => 'Estado del pedido actualizado exitosamente.',
                    'pedido' => $pedido
                ], 200);

            } catch (\Exception $e) {
                \Log::error('Error al actualizar el estado del pedido: ' . $e->getMessage(), ['exception' => $e]);
                return response()->json(['message' => 'Error al actualizar el estado del pedido.', 'error' => $e->getMessage()], 500);
            }
        } else {
            return response()->json(['message' => 'Acceso denegado. Solo administradores pueden cambiar el estado.'], 403);
        }
    }

    /**
     * Display a listing of all orders (for admin).
     * Muestra una lista de todos los pedidos (para uso de administrador).
     */
    public function allOrders()
    {
        if (Auth::check() && Auth::user()->rol === 'admin') {
            $pedidos = Pedidos::with(['direccion', 'productos' => function ($query) {
                                $query->withPivot('cantidad', 'precio_unitario', 'subtotal');
                            }, 'user'])
                                ->orderBy('created_at', 'desc')
                                ->get();

            return response()->json($pedidos);
        } else {
            return response()->json(['message' => 'Acceso denegado. Solo administradores pueden ver todos los pedidos.'], 403);
        }
    }
}
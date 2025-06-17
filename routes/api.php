<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController; 
use App\Http\Controllers\CarritoController; 
use App\Http\Controllers\DireccionController; 
use App\Http\Controllers\PedidoController; // Asegúrate de que este import está aquí

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas de autenticación
Route::post('/login', [AuthController::class, 'login']);

// Rutas públicas (no requieren autenticación)
Route::get('/productos', [ProductoController::class, 'index']);
Route::get('/productos/{producto}', [ProductoController::class, 'show']);
Route::get('/categorias', [CategoriaController::class, 'index']);


// Rutas protegidas por autenticación (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rutas CRUD para Productos (Administrador)
    Route::post('/productos', [ProductoController::class, 'store']); 
    Route::put('/productos/{producto}', [ProductoController::class, 'update']); 
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy']); 

    // Rutas API para el Carrito
    Route::get('/cart', [CarritoController::class, 'index']); 
    Route::post('/cart/add', [CarritoController::class, 'add']); 
    Route::put('/cart/update/{producto}', [CarritoController::class, 'updateQuantity']); 
    Route::delete('/cart/remove/{producto}', [CarritoController::class, 'remove']); 
    Route::post('/cart/clear', [CarritoController::class, 'clear']); 

    // Rutas API para Direcciones
    Route::get('/direcciones', [DireccionController::class, 'index']); 
    Route::post('/direcciones', [DireccionController::class, 'store']); 
    Route::get('/direcciones/{direccion}', [DireccionController::class, 'show']); 
    Route::put('/direcciones/{direccion}', [DireccionController::class, 'update']); 
    Route::delete('/direcciones/{direccion}', [DireccionController::class, 'destroy']); 

    // Rutas API para Pedidos de Usuario
    Route::post('/checkout', [PedidoController::class, 'checkout']); 
    Route::get('/pedidos', [PedidoController::class, 'index']); 
    Route::get('/pedidos/{pedido}', [PedidoController::class, 'show']); 
    
    // ==============================================================================
    // AÑADIDAS: RUTAS DE GESTIÓN DE PEDIDOS PARA ADMINISTRADOR
    // ==============================================================================
    Route::get('/admin/pedidos', [PedidoController::class, 'allOrders']); // Para obtener todos los pedidos (admin)
    Route::put('/pedidos/{pedido}/status', [PedidoController::class, 'updateStatus']); // Para actualizar el estado de un pedido (admin)
    // ==============================================================================

});
<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Puedes personalizar qué excepciones se reportan aquí
        });

        // Este bloque manejará las excepciones de autenticación para API requests
        $this->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], Response::HTTP_UNAUTHORIZED);
            }
            // Si no es una petición JSON, Laravel continuará con su comportamiento predeterminado,
            // que suele ser redirigir a la ruta 'login' para aplicaciones web.
            // Si la ruta 'login' no existe en web.php, entonces se producirá otro error,
            // pero para APIs, esto es lo que buscamos.
        });
    }
}
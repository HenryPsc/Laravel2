<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// *** INICIO DEL CÓDIGO DE DEPURACIÓN DE ERRORES (TEMPORAL) ***
// Este bloque intentará capturar y loguear cualquier excepción crítica
// que no sea manejada por el sistema de excepciones normal de Laravel.
set_exception_handler(function (Throwable $exception) {
    // Intentar loguear la excepción en los logs de PHP (que a menudo van al log del servidor web o php-fpm)
    error_log("CRITICAL_APP_ERROR: " . $exception->getMessage() . "\n" . $exception->getTraceAsString());
    
    // Devolver una respuesta JSON genérica al frontend
    header('Content-Type: application/json', true, 500);
    echo json_encode(['message' => 'Internal Server Error. Check server logs for details.']);
    exit; // Terminar la ejecución para evitar más errores
});
// *** FIN DEL CÓDIGO DE DEPURACIÓN DE ERRORES (TEMPORAL) ***

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
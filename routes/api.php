<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckRoleadmin;
use App\Http\Controllers\MesaController;
use App\Http\Controllers\RestauranteController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CompraController;

Route::post('/comprar', [CompraController::class, 'store'])
     ->middleware(['auth:api']); // Asegúrate de que la ruta esté protegida por autenticación JWT



    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->middleware('jwt.auth');
    Route::get('/me', [LoginController::class, 'me'])->middleware('jwt.auth');

Route::prefix('superadmin')
    ->middleware(\App\Http\Middleware\CheckRole::class) // Protege las rutas con JWT y el middleware CheckRole
    ->group(function () {
        // Obtener todos los restaurantes
        Route::get('restaurante/', [RestauranteController::class, 'index']);
        // Obtener un restaurante específico
        Route::get('restaurante/{id}', [RestauranteController::class, 'show']);
        // Eliminar un restaurante
        Route::delete('restaurante/{id}', [RestauranteController::class, 'destroy']);
        Route::patch('usuarios/{usuario_id}/cambiar-rol', [RoleController::class, 'cambiarRol']);
    });
    Route::middleware(['auth:api'])->group(function () {
        Route::post('/restaurantes', [RestauranteController::class, 'store']);
        Route::put('/restaurantes/{id}', [RestauranteController::class, 'update']);
        
    });

   

    Route::prefix('admin')->middleware(\App\Http\Middleware\CheckRoleadmin::class) // Protege las rutas con JWT y el middleware CheckRole
    ->group(function () {
        Route::apiResource('mesas', MesaController::class);
        Route::put('/mesas/{mesa_id}', [MesaController::class, 'update']);
        Route::post('mesas/{mesa_id}/disponibilidad', [MesaController::class, 'agregarDisponibilidad']);
        Route::delete('disponibilidad/{disponibilidad_id}', [MesaController::class, 'eliminarDisponibilidad']);
    // Reservas
        Route::get('/reservas', [ReservaController::class, 'index']);
        Route::get('/reservas/{reserva_id}', [ReservaController::class, 'show']);
        Route::post('/reservas', [ReservaController::class, 'store']);
        Route::put('/reservas/{reserva_id}', [ReservaController::class, 'update']);
        Route::delete('/reservas/{reserva_id}', [ReservaController::class, 'destroy']);
        Route::patch('/reservas/{reserva_id}/estado', [ReservaController::class, 'cambiarEstado']);
    });
    Route::prefix('cliente')->group(function () {
        // Registro de cliente
        Route::post('register', [UsuarioController::class, 'register']);
        // Información del cliente autenticado
        Route::get('me', [UsuarioController::class, 'me'])
            ->middleware('auth:api');  // Middleware para asegurar que el cliente esté autenticado
    });


    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']) ->middleware([\App\Http\Middleware\CheckRole::class]);
    Route::get('/me', [AuthController::class, 'me']);


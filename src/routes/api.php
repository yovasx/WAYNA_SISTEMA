<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\ProductoController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json([
        'ok' => true,
        'mensaje' => 'API WAYNA activa',
    ]);
});

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('api.auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('api.auth.me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
    });
});

Route::get('/categorias', [CategoriaController::class, 'index'])->name('api.categorias.index');
Route::get('/categorias/{categoria}', [CategoriaController::class, 'show'])->name('api.categorias.show');

Route::get('/productos', [ProductoController::class, 'index'])->name('api.productos.index');
Route::get('/productos/{producto}', [ProductoController::class, 'show'])->name('api.productos.show');

Route::middleware(['auth:sanctum', 'rol:admin'])->group(function () {
    Route::post('/categorias', [CategoriaController::class, 'store'])->name('api.categorias.store');
    Route::put('/categorias/{categoria}', [CategoriaController::class, 'update'])->name('api.categorias.update');
    Route::patch('/categorias/{categoria}', [CategoriaController::class, 'update']);
    Route::delete('/categorias/{categoria}', [CategoriaController::class, 'destroy'])->name('api.categorias.destroy');
});

Route::middleware(['auth:sanctum', 'rol:admin,emprendedor'])->group(function () {
    Route::post('/productos', [ProductoController::class, 'store'])->name('api.productos.store');
    Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('api.productos.update');
    Route::patch('/productos/{producto}', [ProductoController::class, 'update']);
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('api.productos.destroy');
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{
    AuthController,
    UserController,
    CategoryController,
    PostController,
    CommentController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí van todas las rutas de tu API, con el prefijo /api.
| Los endpoints de autenticación son públicos; todo lo demás requiere
| un token válido de Sanctum.
|
*/

// Rutas públicas de autenticación
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Rutas protegidas con Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Logout
    Route::post('logout', [AuthController::class, 'logout']);

    // Perfil del usuario autenticado
    Route::get('users/me', [UserController::class, 'me']);

    // CRUD Usuarios
    Route::apiResource('users', UserController::class);

    // CRUD Categorías
    Route::apiResource('categories', CategoryController::class);

    // CRUD Posts
    Route::apiResource('posts', PostController::class);

    // Comentarios anidados (shallow routing):
    //  • GET    /posts/{post}/comments
    //  • POST   /posts/{post}/comments
    //  • GET    /comments/{comment}
    //  • PUT    /comments/{comment}
    //  • DELETE /comments/{comment}
    Route::apiResource('posts.comments', CommentController::class)
        ->shallow();

    Route::middleware(['auth:sanctum', 'can:manage-users'])->group(function () {
        Route::patch('users/{user}/role', [UserController::class, 'toggleAdmin']);
    });
});

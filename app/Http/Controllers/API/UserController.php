<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

/**
 * UserController
 *
 * @OA\Tag(
 *     name="Users",
 *     description="Endpoints para gestionar usuarios"
 * )
 */
class UserController extends Controller
{
    /**
     * Listar todos los usuarios
     *
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Users"},
     *     @OA\Response(response=200, description="Listado de usuarios")
     * )
     */
    public function index(): Response
    {
        $users = User::all();
        return response($users, 200);
    }

    /**
     * Mostrar un usuario
     *
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Datos del usuario"),
     *     @OA\Response(response=404, description="Usuario no encontrado")
     * )
     */
    public function show(int $id): Response
    {
        $user = User::findOrFail($id);
        return response($user, 200);
    }

    /**
     * Crear un nuevo usuario
     *
     * @OA\Post(
     *     path="/api/users",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Usuario creado")
     * )
     */
    public function store(Request $request): Response
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        return response($user, 201);
    }

    /**
     * Actualizar un usuario existente
     *
     * @OA\Put(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Usuario actualizado"),
     *     @OA\Response(response=404, description="Usuario no encontrado")
     * )
     */
    public function update(Request $request, int $id): Response
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|unique:users,email,' . $user->id,
        ]);

        $user->update($data);
        return response($user, 200);
    }

    /**
     * Eliminar un usuario
     *
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Usuario eliminado"),
     *     @OA\Response(response=404, description="Usuario no encontrado")
     * )
     */
    public function destroy(int $id): Response
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response(null, 204);
    }
}
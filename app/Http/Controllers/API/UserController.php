<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    /**
     * Listar usuarios paginados
     */
    public function index(): AnonymousResourceCollection
    {
        return UserResource::collection(User::paginate(15));
    }

    /**
     * Mostrar perfil del usuario autenticado
     */
    public function me(): UserResource
    {
        return new UserResource(auth()->user());
    }

    /**
     * Mostrar un usuario específico
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    /**
     * Crear un nuevo usuario
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Forzamos is_admin=false si quien crea no es admin
        if (!auth()->user()->is_admin) {
            unset($data['is_admin']);
        }

        $user = User::create($data);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Actualizar un usuario existente
     */
    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $this->authorize('update', $user);
        $user->update($request->validated());

        return new UserResource($user);
    }

    /**
     * Eliminar un usuario
     */
    public function destroy(User $user): Response
    {
        $this->authorize('delete', $user);
        $user->delete();

        return response()->noContent();
    }

    public function toggleAdmin(User $user): UserResource
    {
        // Solo admins llegan aquí (can:manage-users)
        $user->is_admin = !$user->is_admin;
        $user->save();

        return new UserResource($user);
    }

}

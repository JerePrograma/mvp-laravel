<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Registrar un nuevo usuario y retornar token
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated());
        $token = $user->createToken('auth_token')->plainTextToken;

        return (new UserResource($user))
            ->additional([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Login de usuario y retorno de token
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Credenciales inválidas.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return (new UserResource($user))
            ->additional([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ])
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Logout y revocación de tokens
     */
    public function logout(): Response
    {
        /** @var User $user */
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->noContent();
    }
}

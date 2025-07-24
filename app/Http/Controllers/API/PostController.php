<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends Controller
{
    /**
     * Listar posts paginados
     */
    public function index(): AnonymousResourceCollection
    {
        return PostResource::collection(
            Post::with(['category', 'user'])->paginate(10)
        );
    }

    /**
     * Mostrar un post específico
     */
    public function show(Post $post): PostResource
    {
        return new PostResource(
            $post->load(['comments.user', 'category', 'user'])
        );
    }

    /**
     * Crear un nuevo post
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        $post = auth()->user()->posts()->create(
            $request->validated()
        );

        return (new PostResource($post))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Actualizar un post existente
     */
    public function update(UpdatePostRequest $request, Post $post): PostResource
    {
        $this->authorize('update', $post);
        $post->update($request->validated());

        return new PostResource($post);
    }

    /**
     * Eliminar un post
     */
    public function destroy(Post $post): Response
    {
        $this->authorize('delete', $post);
        $post->delete();

        return response()->noContent();
    }
}

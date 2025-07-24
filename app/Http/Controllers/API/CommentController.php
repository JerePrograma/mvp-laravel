<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    /**
     * Listar comentarios de un post
     */
    public function index(Post $post): AnonymousResourceCollection
    {
        return CommentResource::collection(
            $post->comments()->with('user')->get()
        );
    }

    /**
     * Mostrar un comentario específico
     */
    public function show(Comment $comment): CommentResource
    {
        return new CommentResource(
            $comment->load('user')
        );
    }

    /**
     * Crear un comentario en un post
     */
    // CommentController.php
    public function store(StoreCommentRequest $request, Post $post)
    {
        $data = $request->validated();

        // creamos el comentario "ad-hoc" desde el usuario autenticado
        $comment = $request->user()
            ->comments()
            ->create(array_merge($data, [
                'post_id' => $post->id
            ]));

        return (new CommentResource($comment))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    /**
     * Actualizar un comentario
     */
    public function update(UpdateCommentRequest $request, Comment $comment): CommentResource
    {
        $this->authorize('update', $comment);
        $comment->update($request->validated());

        return new CommentResource($comment);
    }

    /**
     * Eliminar un comentario
     */
    public function destroy(Comment $comment): Response
    {
        $this->authorize('delete', $comment);
        $comment->delete();

        return response()->noContent();
    }
}

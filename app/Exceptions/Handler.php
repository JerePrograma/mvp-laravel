<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Excepciones que no queremos reportar.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        AuthenticationException::class,
        AuthorizationException::class,
        ValidationException::class,
        ModelNotFoundException::class,
        NotFoundHttpException::class,
        ThrottleRequestsException::class,
    ];

    /**
     * Inputs que no deben volver en 'old' tras validación fallida.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        // 422: Validación
        $this->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Error de validación.',
                    'errors' => $e->errors(),
                ], HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        });

        // 401: No autenticado
        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'No autenticado.',
                ], HttpResponse::HTTP_UNAUTHORIZED);
            }
        });

        // 403: Falló Policy / Gate
        $this->renderable(function (AuthorizationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Acción no autorizada.',
                ], HttpResponse::HTTP_FORBIDDEN);
            }
        });

        // 403: AccessDeniedHttpException (Symfony)
        $this->renderable(function (AccessDeniedHttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Acceso denegado.',
                ], HttpResponse::HTTP_FORBIDDEN);
            }
        });

        // 404: Modelo o ruta no encontrada
        $this->renderable(function (ModelNotFoundException|NotFoundHttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Recurso no encontrado.',
                ], HttpResponse::HTTP_NOT_FOUND);
            }
        });

        // 429: Límite de peticiones
        $this->renderable(function (ThrottleRequestsException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Muchas solicitudes. Intenta más tarde.',
                ], HttpResponse::HTTP_TOO_MANY_REQUESTS);
            }
        });

        // 4xx / 5xx genérico
        $this->renderable(function (HttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $status = $e->getStatusCode();
                $message = $e->getMessage() ?: (HttpResponse::$statusTexts[$status] ?? 'Error HTTP');

                return response()->json([
                    'message' => $message,
                ], $status);
            }
        });

        // 500: Fallback error interno
        $this->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $payload = ['message' => 'Error interno del servidor.'];

                if (config('app.debug')) {
                    $payload['exception'] = get_class($e);
                    $payload['trace'] = collect($e->getTrace())
                        ->map(fn($frame) => Arr::only($frame, ['file', 'line', 'function']))
                        ->all();
                }

                return response()->json($payload, HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
        });
    }

    /**
     * Convierte una AuthenticationException en JSON para peticiones API.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'No autenticado.',
            ], HttpResponse::HTTP_UNAUTHORIZED);
        }

        return parent::unauthenticated($request, $exception);
    }
}

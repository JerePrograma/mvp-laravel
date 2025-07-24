<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Arr;                                     // ← Faltaba esto
use Symfony\Component\HttpFoundation\Response as HttpResponse;   // ← Faltaba esto
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        AuthenticationException::class,
        ValidationException::class,
        ModelNotFoundException::class,
        NotFoundHttpException::class,
        ThrottleRequestsException::class,
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        //
    }

    public function render($request, Throwable $exception)
    {
        // Cambié wantsJson() por expectsJson()
        if ($request->expectsJson() || $request->is('api/*')) {
            $status = 500;
            $payload = ['message' => 'Error interno del servidor.'];

            if (
                $exception instanceof ModelNotFoundException ||
                $exception instanceof NotFoundHttpException
            ) {
                $status = 404;
                $payload['message'] = 'Recurso no encontrado.';
            }

            if ($exception instanceof ValidationException) {
                $status = 422;
                $payload = [
                    'message' => 'Error de validación.',
                    'errors' => $exception->errors(),
                ];
            }

            if (
                $exception instanceof AuthenticationException ||
                $exception instanceof UnauthorizedHttpException
            ) {
                $status = 401;
                $payload['message'] = 'No autenticado.';
            }

            if ($exception instanceof ThrottleRequestsException) {
                $status = 429;
                $payload['message'] = 'Muchas solicitudes. Intenta más tarde.';
            }

            if ($exception instanceof HttpException) {
                $status = $exception->getStatusCode();
                // Ahora HttpResponse hace referencia a Symfony\Component\HttpFoundation\Response
                $payload['message'] = $exception->getMessage()
                    ?: (HttpResponse::$statusTexts[$status] ?? 'Error HTTP');
            }

            if (config('app.debug')) {
                $payload['exception'] = get_class($exception);
                $payload['trace'] = collect($exception->getTrace())
                    ->map(fn($frame) => Arr::only($frame, ['file', 'line', 'function']))
                    ->all();
            }

            return response()->json($payload, $status);
        }

        return parent::render($request, $exception);
    }


    /**
     * Convierte una AuthenticationException en JSON en peticiones API.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'No autenticado.',
            ], 401);
        }

        // Si fuera web (no API), redirigir al login tradicional
        return redirect()->guest(route('login'));
    }
}

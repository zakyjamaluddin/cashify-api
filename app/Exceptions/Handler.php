<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($request->wantsJson()) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // Cek apakah permintaan mengharapkan respons JSON (API Request)
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Unauthenticated. Token tidak valid atau tidak tersedia.',
                'code' => 401,
            ], 401);
        }
        return response()->json([
                'message' => 'Unauthenticated. Token tidak valid atau tidak tersedia.',
                'code' => 401,
            ], 401);

        // Untuk permintaan web, tetap alihkan ke halaman login
        // return redirect()->guest(route('login'));
    }

    private function handleApiException($request, Throwable $exception)
    {
        $exception = $this->prepareException($exception);

        if ($exception instanceof \Illuminate\Http\Exceptions\HttpResponseException) {
            $exception = $exception->getResponse();
        }

        // if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
        //     return response()->json(['message' => 'Unauthenticated.'], 401);
        // }

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $exception->errors(),
            ], 422);
        }

        if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
            return response()->json(['message' => 'This action is unauthorized.'], 403);
        }

        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json(['message' => 'Entry for '.str_replace('App\\Models\\', '', $exception->getModel()).' not found'], 404);
        }

        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return response()->json(['message' => 'The specified URL cannot be found.'], 404);
        }

        if ($exception instanceof \Illuminate\Database\QueryException) {
            if (config('app.debug')) {
                return response()->json([
                    'message' => 'Database query error.',
                    'error' => $exception->getMessage()
                ], 500);
            }
            return response()->json(['message' => 'A database error occurred.'], 500);
        }

        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            return response()->json(['message' => $exception->getMessage()], $exception->getStatusCode());
        }

        if (config('app.debug')) {
            return response()->json([
                'message' => $exception->getMessage(),
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTrace()
            ], 500);
        }

        return response()->json(['message' => 'Unexpected error. Please try again later.'], 500);
    }
}

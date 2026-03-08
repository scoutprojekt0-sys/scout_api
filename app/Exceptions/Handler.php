<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        // API istekleri için JSON response döndür
        if ($request->is('api/*') || $request->expectsJson()) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    protected function handleApiException($request, Throwable $e)
    {
        $status = 500;
        $message = 'Sunucu hatası oluştu.';

        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            $status = 404;
            $message = 'Kayıt bulunamadı.';
        } elseif ($e instanceof AuthenticationException) {
            $status = 401;
            $message = 'Kimlik doğrulama gerekli.';
        } elseif ($e instanceof AccessDeniedHttpException) {
            $status = 403;
            $message = 'Bu işlem için yetkiniz yok.';
        } elseif ($e instanceof ValidationException) {
            return response()->json([
                'ok' => false,
                'message' => 'Validasyon hatası.',
                'errors' => $e->errors(),
            ], 422);
        } elseif (method_exists($e, 'getStatusCode')) {
            $status = $e->getStatusCode();
            $message = $e->getMessage();
        }

        // Production'da hata detaylarını gizle
        if (config('app.debug')) {
            return response()->json([
                'ok' => false,
                'message' => $message,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], $status);
        }

        return response()->json([
            'ok' => false,
            'message' => $message,
        ], $status);
    }
}

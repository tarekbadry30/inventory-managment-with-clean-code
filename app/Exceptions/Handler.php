<?php

namespace App\Exceptions;

use Throwable;
use InvalidArgumentException;

use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

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
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }


    public function render($request, Throwable $e): Response
    {
        // Log all exceptions
        Log::error($e);

        // Validation error
        if ($e instanceof ValidationException) {
            return $this->responder($request, $e->getMessage(), [$e->validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // AuthenticationException error
        if ($e instanceof AuthenticationException) {
            return $this->responder($request, $e->getMessage(), [], Response::HTTP_UNAUTHORIZED);
        }

        // Not found
        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            return $this->responder($request, 'resouce not found', [], Response::HTTP_NOT_FOUND);
        }

        // Forbidden
        if ($e instanceof AuthorizationException) {
            return $this->responder($request, $e->getMessage(), [], Response::HTTP_FORBIDDEN);
        }

        // Bad request
        if ($e instanceof InvalidArgumentException) {
            return $this->responder($request, $e->getMessage(), [], Response::HTTP_BAD_REQUEST);
        }

        // Unprocessable
        if ($e instanceof UnprocessableEntityHttpException) {
            return $this->responder($request, $e->getMessage(), [], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Fallback to parent render for other exceptions
        return $this->responder($request, 'An unexpected error occurred.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    protected function responder($request, $message, $data = [], $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR): Response
    {
        if ($request->expectsJson()) {
            return sendErrorResponse($message, $data, $statusCode);
        }

        if ($statusCode === Response::HTTP_UNPROCESSABLE_ENTITY) {
            return redirect()->back()->withErrors($data)->withInput();
        }

        abort($statusCode, $message);
    }
}

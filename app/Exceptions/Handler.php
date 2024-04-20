<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
            if ($exception instanceof ValidationException) {
                return $this->renderValidationExceptionJson($exception);
            }

            // Handle generic exceptions
            return $this->renderGenericExceptionJson($exception);
        }

        return parent::render($request, $exception);
    }

    protected function renderValidationExceptionJson(ValidationException $exception)
    {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $exception->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function renderGenericExceptionJson(Throwable $exception)
    {
        return response()->json([
            'message' => 'An error occurred',
            'error' => $exception->getMessage(),
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

}
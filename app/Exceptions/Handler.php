<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
            $response = [
                'status_code' => 500,
                'status' => false,
                'errors' => [],
                'message' => '500, Internal server error.',
                'data' => [],
            ];
            if ($exception instanceof NotFoundHttpException) {
                $response['status_code'] = 404;
                $response['message'] = '404, Not Found';
                return response()->json($response);
            }
            if ($exception instanceof AuthenticationException) {
                $response['status_code'] = 401;
                $response['message'] = '401, Unauthorized request';
                return response()->json($response);
            }
            if ($exception instanceof MethodNotAllowedHttpException) {
                $response['status_code'] = 405;
                $response['message'] = '405, Method Not allowed.';
                return response()->json($response);
            }
        } else {
            if ($exception instanceof TokenMismatchException) {
                return redirect('/');
            }
        }
        return parent::render($request, $exception);
    }
}

<?php

namespace App\Exceptions;

use Throwable;
use Psr\Log\LogLevel;
use Illuminate\Http\Request;
use App\Exceptions\ModelNotDefined;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\Mailer\Exception\HttpTransportException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'message' =>
                            'You are not authorized to access this resource'
                    ]
                ], 403);
            }
        });

        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'message' =>
                            'The resource was not found in the database'
                    ]
                ], 404);
            }
        });

        $this->renderable(function (ModelNotDefined $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'message' =>
                            'No model defined'
                    ]
                ], 500);
            }
        });

        // $this->renderable(function (HttpTransportException $e, Request $request) {
        //     if ($request->expectsJson()) {
        //         return response()->json([
        //             'errors' => [
        //                 'message' =>
        //                     'Incorrect email address'
        //             ]
        //         ], 422);
        //     }
        // });
    }
}

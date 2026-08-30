<?php

namespace App\Exceptions;

use App\Exceptions\Api\ApiException;
use App\Exceptions\Pterodactyl\PterodactylException;
use App\Exceptions\Server\ServerException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Throwable;

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
        $this->reportable(function (Throwable $e) {
            //
        });

        // Render API exceptions as JSON with their HTTP status code.
        $this->renderable(function (ApiException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], $e->getStatusCode());
            }

            return null;
        });

        // Render Pterodactyl exceptions with a mapped status code.
        $this->renderable(function (PterodactylException $e, $request) {
            $status = $e->getStatusCode() ?? 500;

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], $status);
            }

            return null;
        });

        // Render server exceptions with their status code.
        $this->renderable(function (ServerException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], $e->getStatusCode());
            }

            return null;
        });
    }


    /**
     * @param $request
     * @param Throwable $exception
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($this->isHttpException($exception)) {
            if (view()->exists('errors.' . $exception->getStatusCode())) {
                return response()->view(
                    'errors.' . $exception->getStatusCode(),
                    ['exception' => $exception],
                    $exception->getStatusCode()
                );
            }
        }

        // Fallback to default behavior for non-HTTP exceptions
        return parent::render($request, $exception);
    }
}

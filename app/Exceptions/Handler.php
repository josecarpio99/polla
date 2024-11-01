<?php

namespace App\Exceptions;

use Throwable;
use App\Libraries\Core;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        /* only run if debug is turn off */
        if (!env('APP_DEBUG', true)) {

            $core = new Core;

            /* handling 404 exception */
            if ($exception instanceof NotFoundHttpException) {
                return response()->json([
                    'error' => 'Object not Found',
                ])->setStatusCode(404);
            }
            /* handling 500 exception */
            $exception_name = get_class($exception);

            $error = $core->log('error', "Exception ($exception_name) : " . $exception->getTraceAsString(), true);

            return response()->json([
                'error' => "Server problem, code [$error]",
            ])->setStatusCode(500);
        }

        return parent::render($request, $exception);
    }
}

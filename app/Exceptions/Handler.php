<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
    }

    /**
     * @param  Request  $request
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof HttpException && $e->getStatusCode() === 403) {
            $view = ($request->is('admin*') || $request->is('permissions*'))
                ? 'errors.403_admin'
                : 'errors.403';

            return response()->view($view, [
                'exception' => $e,
                'message' => $e->getMessage(),
            ], 403);
        }

        return parent::render($request, $e);
    }
}

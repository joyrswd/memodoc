<?php

namespace App\Exceptions;

use App\Exceptions\MailReport;
use App\Exceptions\ErrorRenderer;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    private MailReport $mailReport;
    private ErrorRenderer $errorRenderer;

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

    public function __construct(Container $container, MailReport $mailReport, ErrorRenderer $errorRenderer)
    {
        $this->mailReport = $mailReport;
        $this->errorRenderer = $errorRenderer;
        parent::__construct($container);
    }

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Report or log an exception.
     */
    public function report(Throwable $exception)
    {
        if ($this->shouldReport($exception)) {
            $this->mailReport->send($exception);
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AuthenticationException
        || $exception instanceof ValidationException
        || $request->expectsJson()) {
            return parent::render($request, $exception);
        }
        $preparedException = $this->prepareException($exception);
        return $this->errorRenderer->render($preparedException, $this->isHttpException($preparedException));
    }

}

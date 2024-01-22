<?php

namespace App\Exceptions;

use App\Exceptions\MailReport;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    private MailReport $mailReport;

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

    public function __construct(Container $container, MailReport $mailReport)
    {
        $this->mailReport = $mailReport;
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
        $code = $this->isHttpException($exception) ? $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
        $title = $this->getStatusTitle($code);
        return response()->view('common.error', compact('code', 'title'), $code);
    }

    /**
     * Get status title
     */
    private function getStatusTitle(int $code): string
    {
        $reflection = new \ReflectionClass(Response::class);
        $codes = $reflection->getConstants();
        $key = array_search($code, $codes);
        if (empty($key)) {
            return 'Unknown Error Occurred';
        }
        return $this->generateTitle($key);
    }

    /**
     * Generate title
     */
    private function generateTitle(string $label): string
    {
        $lower = strtolower($label);
        $phrase = str_replace(['http_','_'], ['', ' '], $lower);
        return ucwords($phrase);
    }

}

<?php

namespace App\Exceptions;
use Symfony\Component\HttpFoundation\Response;

class ErrorRenderer
{
    /**
     * Render error
     */
    public function render(\Throwable $exception, bool $isException): Response
    {
        $code = $this->getStatusCode($exception, $isException);
        $title = $this->getStatusTitle($code);
        return response()->view('common.error', compact('code', 'title'), $code);
    }

    /**
     * Get status code
     */
    private function getStatusCode(\Throwable $exception, bool $isException): int
    {
        return $isException ? $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
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

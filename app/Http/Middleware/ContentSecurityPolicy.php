<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $config = config('csp', []);
        if (empty($config) === false) {
            $nonce = $this->generateNonce();
            $csp = $this->generateCsp($config['directives'], $config['hosts'], $nonce);
            return $next($request)->header('Content-Security-Policy', $csp);
        }
        return $next($request);
    }

    /**
     * Set CSP header
     */
    private function generateCsp(array $directives, array $hosts, string $nonce): string
    {
        foreach ($directives as $directive => $value) {
            $swapped = Str::swap($hosts, $value);
            $nonced = Str::replaceFirst("'nonce'", "'nonce-{$nonce}'", $swapped);
            $csp[] = $directive.' '.$nonced;
        }
        return implode(';', $csp);
    }

    /**
     * Generate nonce
     */
    private function generateNonce(): string
    {
        $nonce = Str::random(32);
        view()->share('nonce', $nonce);
        return $nonce;
    }
}

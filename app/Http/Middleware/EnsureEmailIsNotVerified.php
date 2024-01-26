<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class EnsureEmailIsNotVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            // ログインしていない場合はホーム画面へリダイレクト
            return redirect()->route('home');
        } elseif(! $request->user() instanceof MustVerifyEmail
            || $request->user()->hasVerifiedEmail()) {
            // メール認証済みの場合は直前のページへリダイレクト
            return back();
        }
        return $next($request);
    }
}

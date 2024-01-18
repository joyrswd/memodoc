<?php

namespace App\Providers;

use App\Services\MemoService;
use App\Services\ApiJobService;
use App\Services\DocumentService;
use App\Services\LoginService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        // {memo}IDの作成者がログインユーザーと一致するかチェック
        Route::bind('memo', MemoService::class);
        // {doc}IDの作成者がログインユーザーと一致するかチェック
        Route::bind('doc', DocumentService::class);
        // {job}IDの作成者がログインユーザーと一致するかチェック
        Route::bind('job', ApiJobService::class);
        // {token}が有効かチェック
        Route::bind('token', LoginService::class);
    }
}

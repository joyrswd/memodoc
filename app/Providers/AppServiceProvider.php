<?php

namespace App\Providers;

use App\Services\MemoService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(MemoService $memoService): void
    {
        // {memo}の作成者がログインユーザーと一致するかチェック
        Route::bind('memo', function ($value) use ($memoService) {
            if ($memoService->getMemo(auth()->user()->id, $value)) {
                return $value;
            }
            abort(404);
        });
    }
}

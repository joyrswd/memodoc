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
            $int = filter_var($value, FILTER_VALIDATE_INT);
            if (is_int($int) && $memoService->getMemo(auth()->user()->id, $int)) {
                return $int;
            }
            abort(404);
        });
    }
}

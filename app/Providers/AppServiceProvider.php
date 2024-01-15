<?php

namespace App\Providers;

use App\Services\MemoService;
use App\Services\OpenAiApiService;
use App\Interfaces\AiApiServiceInterface;
use App\Services\DocumentService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //AiApiServiceInterfaceにOpenAiApiServiceを割り当てる
        $this->app->singleton(AiApiServiceInterface::class, function ($app) {
            return new OpenAiApiService(config('api.' . OpenAiApiService::KEY, []));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(MemoService $memoService, DocumentService $documentService): void
    {
        // {memo}の作成者がログインユーザーと一致するかチェック
        Route::bind('memo', function ($value) use ($memoService) {
            $int = filter_var($value, FILTER_VALIDATE_INT);
            if (is_int($int) && $memoService->getMemo(auth()->id(), $int)) {
                return $int;
            }
            abort(404);
        });
        // {doc}の作成者がログインユーザーと一致するかチェック
        Route::bind('doc', function ($value) use ($documentService) {
            $int = filter_var($value, FILTER_VALIDATE_INT);
            if (is_int($int) && $documentService->getDocument(auth()->id(), $int)) {
                return $int;
            }
            abort(404);
        });
    }
}

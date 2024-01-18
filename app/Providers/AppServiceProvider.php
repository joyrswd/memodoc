<?php

namespace App\Providers;

use App\Services\OpenAiApiService;
use App\Interfaces\AiApiServiceInterface;
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
    public function boot(): void
    {
        //
    }
}

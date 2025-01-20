<?php

namespace App\Providers;

use App\Core\KTBootstrap;
use App\Services\DeepseekService;
use GuzzleHttp\Client;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot(): void
    {
        Builder::defaultStringLength(191);

        KTBootstrap::init();

        $this->app->singleton(DeepseekService::class, function () {
            return new DeepseekService(new Client([
                'base_uri' => config('services.deepseek.base_uri'),
                'headers' => [
                    'Authorization' => 'Bearer ' . config('services.deepseek.api_key'),
                    'Content-Type' => 'application/json',
                ],
            ]));
        });
    }
}

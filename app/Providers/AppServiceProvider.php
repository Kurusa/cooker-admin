<?php

namespace App\Providers;

use App\Core\KTBootstrap;
use App\Services\DeepseekService;
use App\Services\Parsers\Parsers\FayniReceptyParser;
use App\Services\Parsers\Parsers\JistyParser;
use App\Services\Parsers\Parsers\NovaStravaParser;
use App\Services\Parsers\Parsers\PatelnyaParser;
use App\Services\Parsers\Parsers\PicanteParser;
use App\Services\Parsers\Parsers\RetseptyParser;
use App\Services\Parsers\Parsers\RudParser;
use App\Services\Parsers\Parsers\SmachnoParser;
use App\Services\Parsers\Parsers\TandiParser;
use App\Services\Parsers\Parsers\TsnParser;
use App\Services\Parsers\Parsers\UaReceptParser;
use App\Services\Parsers\Parsers\VseReceptyParser;
use App\Services\Parsers\RecipeParserFactory;
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

        $this->app->singleton(RecipeParserFactory::class, function () {
            $factory = new RecipeParserFactory();
            $factory->registerParser('patelnya', PatelnyaParser::class);
            $factory->registerParser('fayni', FayniReceptyParser::class);
            $factory->registerParser('jisty', JistyParser::class);
            $factory->registerParser('novastrava', NovaStravaParser::class);
            $factory->registerParser('rud', RudParser::class);
            $factory->registerParser('tsn', TsnParser::class);
            $factory->registerParser('smachno', SmachnoParser::class);
            $factory->registerParser('picante', PicanteParser::class);
            $factory->registerParser('retsepty', RetseptyParser::class);
            $factory->registerParser('vse-recepty', VseReceptyParser::class);
            $factory->registerParser('ua-recept', UaReceptParser::class);
            $factory->registerParser('tandi', TandiParser::class);
            return $factory;
        });
    }
}

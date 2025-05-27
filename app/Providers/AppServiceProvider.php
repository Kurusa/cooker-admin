<?php

namespace App\Providers;

use App\Services\AiProviders\DeepseekService;
use App\Services\AiProviders\GeminiService;
use App\Services\Parsers\Contracts\HtmlCleanerInterface;
use App\Services\Parsers\HtmlCleaner;
use App\Services\Parsers\Parsers\FayniReceptyParser;
use App\Services\Parsers\Parsers\FoodcourtParser;
use App\Services\Parsers\Parsers\JistyParser;
use App\Services\Parsers\Parsers\NovaStravaParser;
use App\Services\Parsers\Parsers\PatelnyaParser;
use App\Services\Parsers\Parsers\PicanteParser;
use App\Services\Parsers\Parsers\RudParser;
use App\Services\Parsers\RecipeParserFactory;
use App\Services\Parsers\Steps\RemoveClassesAndAttributesStep;
use App\Services\Parsers\Steps\RemoveCommentsStep;
use App\Services\Parsers\Steps\RemoveEmptyDivsStep;
use App\Services\Parsers\Steps\RemoveGlobalJunkNodesStep;
use App\Services\Parsers\Steps\RemoveImageAttributesStep;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->singleton(DeepseekService::class, function () {
            return new DeepseekService(new Client([
                'base_uri' => config('services.deepseek.base_uri'),
                'headers' => [
                    'Authorization' => 'Bearer ' . config('services.deepseek.api_key'),
                    'Content-Type' => 'application/json',
                ],
            ]));
        });

        $this->app->singleton(GeminiService::class, function () {
            return new GeminiService(new Client([
                'base_uri' => 'https://generativelanguage.googleapis.com/v1beta/models/',
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'timeout' => 30,
            ]));
        });

        $this->app->singleton(RecipeParserFactory::class, function () {
            $factory = new RecipeParserFactory();
            $factory->registerParser('patelnya', PatelnyaParser::class);
            $factory->registerParser('fayni', FayniReceptyParser::class);
            $factory->registerParser('novastrava', NovaStravaParser::class);
            $factory->registerParser('rud', RudParser::class);
            $factory->registerParser('jisty', JistyParser::class);
            $factory->registerParser('foodcourt', FoodcourtParser::class);
            $factory->registerParser('picante', PicanteParser::class);
//            $factory->registerParser('tsn', TsnParser::class);
//            $factory->registerParser('smachno', SmachnoParser::class);
//            $factory->registerParser('retsepty', RetseptyParser::class);
//            $factory->registerParser('vse-recepty', VseReceptyParser::class);
//            $factory->registerParser('ua-recept', UaReceptParser::class);
//            $factory->registerParser('tandi', TandiParser::class);
//            $factory->registerParser('allrecipes', AllRecipesParser::class);
//            $factory->registerParser('monchef', MonchefParser::class);
//            $factory->registerParser('receptytv', ReceptyTvParser::class);
//            $factory->registerParser('yabpoela', YabpoelaParser::class);
//            $factory->registerParser('etnocook', EtnocookParser::class); // китайсько-англійське?
            return $factory;
        });

        $this->app->bind(HtmlCleanerInterface::class, function () {
            return new HtmlCleaner([
                new RemoveClassesAndAttributesStep(),
                new RemoveCommentsStep(),
                new RemoveEmptyDivsStep(),
                new RemoveGlobalJunkNodesStep(),
                new RemoveImageAttributesStep(),
            ]);
        });
    }
}

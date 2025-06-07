<?php

namespace App\Providers;

use App\Enums\Source\SourceEnum;
use App\Services\AiProviders\DeepseekService;
use App\Services\AiProviders\GeminiService;
use App\Services\AiProviders\OpenAiService;
use App\Services\Parsers\Contracts\HtmlCleanerInterface;
use App\Services\Parsers\HtmlCleaner;
use App\Services\Parsers\RecipeParserFactory;
use App\Services\Parsers\Steps\RemoveClassesAndAttributesStep;
use App\Services\Parsers\Steps\RemoveCommentsStep;
use App\Services\Parsers\Steps\RemoveEmptyDivsStep;
use App\Services\Parsers\Steps\RemoveGlobalJunkNodesStep;
use App\Services\Parsers\Steps\RemoveImageAttributesStep;
use App\Services\Parsers\Steps\RemoveJsEventAttributesStep;
use App\Services\Parsers\Steps\RemoveSpecificTagsStep;
use App\Services\Parsers\Steps\RemoveSvgAttributesStep;
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

        $this->app->singleton(OpenAiService::class, function () {
            return new OpenAIService(new Client([
                'base_uri' => config('services.openai.base_uri'),
                'headers' => [
                    'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                    'Content-Type' => 'application/json',
                ],
            ]));
        });

        $this->app->singleton(GeminiService::class, function () {
            return new GeminiService(new Client([
                'base_uri' => config('services.gemini.base_uri'),
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'timeout' => 30,
            ]));
        });

        $this->app->singleton(RecipeParserFactory::class, function () {
            $factory = new RecipeParserFactory();

            foreach (SourceEnum::cases() as $sourceEnum) {
                $factory->registerParser($sourceEnum->value, $sourceEnum->parserClass());
            }

            return $factory;
        });

        $this->app->bind(HtmlCleanerInterface::class, function () {
            return new HtmlCleaner([
                new RemoveClassesAndAttributesStep(),
                new RemoveCommentsStep(),
                new RemoveGlobalJunkNodesStep(),
                new RemoveImageAttributesStep(),
                new RemoveSpecificTagsStep(),
                new RemoveSvgAttributesStep(),
                new RemoveJsEventAttributesStep(),
                new RemoveEmptyDivsStep(),
            ]);
        });
    }
}

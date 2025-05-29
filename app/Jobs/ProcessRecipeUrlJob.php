<?php

namespace App\Jobs;

use App\Enums\AiProvider;
use App\Models\Source\SourceRecipeUrl;
use App\Notifications\RecipeParsingCompleted;
use App\Notifications\RecipeParsingFailedNotification;
use App\Services\Parsers\RecipeParserFactory;
use App\Services\ProcessRecipeUrlService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class ProcessRecipeUrlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public function __construct(
        public readonly int        $sourceRecipeUrlId,
        public readonly AiProvider $aiProvider,
    )
    {
    }

    public function handle(
        RecipeParserFactory     $parserFactory,
        ProcessRecipeUrlService $service,
    ): void
    {
        /** @var SourceRecipeUrl $sourceRecipeUrl */
        $sourceRecipeUrl = SourceRecipeUrl::with('source')->findOrFail($this->sourceRecipeUrlId);

        $parser = $parserFactory->make($sourceRecipeUrl->source->title);
        $service->processRecipeUrl($sourceRecipeUrl, $parser, $this->aiProvider);
    }

    public function failed(Exception $exception): void
    {
        Notification::route('telegram', config('services.telegram.chat_id'))
            ->notify(new RecipeParsingFailedNotification($exception->getMessage()));
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Recipe;
use App\Services\Parsers\ProcessRecipeUrlService;
use App\Services\Parsers\RecipeParserFactory;
use Illuminate\Console\Command;

class ParseRecipeByIdCommand extends Command
{
    protected $signature = 'parse:recipe {recipeId}';

    protected $description = 'Parse a single recipe by its ID';

    public function __construct(
        private readonly RecipeParserFactory $parserFactory,
        private readonly ProcessRecipeUrlService $processRecipeUrlService,
    )
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $recipeId = $this->argument('recipeId');

        /** @var Recipe $recipe */
        $recipe = Recipe::find($recipeId);

        $parser = $this->parserFactory->make($recipe->source->title);

        $this->processRecipeUrlService->processRecipeUrl($recipe->sourceRecipeUrl, $parser);
    }
}

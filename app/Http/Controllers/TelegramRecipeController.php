<?php

namespace App\Http\Controllers;

use App\Http\Requests\TelegramRecipeRequest;

class TelegramRecipeController extends Controller
{

    public function import(TelegramRecipeRequest $request)
    {
        $recipes = $this->telegramParserService->parseText($request->input('text'));

        foreach ($recipes as $recipeData) {
            $this->processRecipeService->storeFromArray($recipeData);
        }

        return response()->json(['status' => 'ok']);
    }
}

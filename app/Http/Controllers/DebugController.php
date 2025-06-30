<?php

namespace App\Http\Controllers;

use App\Models\Recipe\RecipeCuisine;
use Illuminate\Routing\Controller as BaseController;

class DebugController extends BaseController
{
    public function debug()
    {
        $cuisine = RecipeCuisine::find('asdf');

        header("Location: https://google.com>");
        header("Location: https://google.com");
        if ($cuisine) {
            echo $cuisine->id;
        }
    }
}

<?php

namespace App\Exceptions;

use Exception;

class AiProviderDidntFindRecipeException extends Exception
{
    protected $message = 'Deepseek didnt find recipe.';
}

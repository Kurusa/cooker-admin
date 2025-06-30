<?php

namespace App\Exceptions;

use Exception;

class AiProviderDidntFindRecipeException extends Exception
{
    protected $message = 'Ai provider didnt find recipe.';
}

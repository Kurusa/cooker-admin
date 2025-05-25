<?php

namespace App\Exceptions;

use Exception;

class DeepseekDidntFindRecipeException extends Exception
{
    protected $message = 'Deepseek didnt find recipe.';
}

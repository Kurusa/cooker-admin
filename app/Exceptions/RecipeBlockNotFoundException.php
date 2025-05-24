<?php

namespace App\Exceptions;

use Exception;

class RecipeBlockNotFoundException extends Exception
{
    protected $message = 'Recipe block not found.';
}

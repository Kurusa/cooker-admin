<?php

namespace App\Exceptions;

use Exception;

class UnsupportedCategoryException extends Exception
{
    protected $message = 'Unsupported category.';
}

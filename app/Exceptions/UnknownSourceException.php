<?php

namespace App\Exceptions;

use Exception;

class UnknownSourceException extends Exception
{
    protected $message = 'Unknown source.';
}

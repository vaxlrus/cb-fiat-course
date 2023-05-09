<?php

namespace App\Domain\Exceptions;

use Exception;
use Throwable;

class CBRHostNotAvailable extends Exception
{
    public function __construct(string $message = "", int $code = 504, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

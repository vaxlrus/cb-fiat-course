<?php

namespace App\Domain\Exceptions;

use Exception;
use Throwable;

final class InvalidDataException extends Exception
{
    public function __construct(string $message = "На указанную дату не существует курса валют", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

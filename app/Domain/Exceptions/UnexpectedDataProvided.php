<?php

namespace App\Domain\Exceptions;

use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Throwable;

class UnexpectedDataProvided extends Exception
{
    private PromiseInterface|Response $response;

    public function __construct(string $message = "", PromiseInterface|Response $response = null, ?Throwable $previous = null)
    {
        $this->response = $response;

        parent::__construct($message, 502, $previous);
    }

    public function getResponse(): PromiseInterface|Response
    {
        return $this->response;
    }
}

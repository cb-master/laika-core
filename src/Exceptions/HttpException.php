<?php

namespace Laika\Core\Exceptions;

use Exception;

class HttpException extends Exception
{
    protected int $statusCode;

    public function __construct(int $statusCode = 500, string $message = '', $code = 0)
    {
        parent::__construct($message, $code);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}

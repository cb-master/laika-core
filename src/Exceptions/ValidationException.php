<?php

namespace Laika\Core\Exceptions;

class ValidationException extends HttpException
{
    protected array $errors;

    public function __construct(array $errors, string $message = 'Validation Failed')
    {
        parent::__construct(422, $message);
        $this->errors = $errors;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}

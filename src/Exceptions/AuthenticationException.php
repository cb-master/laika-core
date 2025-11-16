<?php

namespace Laika\Core\Exceptions;

class AuthenticationException extends HttpException
{
    public function __construct(string $message = 'Unauthenticated.')
    {
        parent::__construct(401, $message);
    }
}

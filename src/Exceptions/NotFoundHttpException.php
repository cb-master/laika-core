<?php

namespace Laika\Core\Exceptions;

class NotFoundHttpException extends HttpException
{
    public function __construct(string $message = 'Page Not Found')
    {
        parent::__construct(404, $message);
    }
}

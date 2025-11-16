<?php

namespace Laika\Core\Http;

class ApiResponse
{
    public static function success(
        array $data = [],
        string $message = 'Success',
        int $status = 200,
        array $meta = []
    ) {
        http_response_code($status);

        echo json_encode([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
            'meta'    => $meta,
        ], JSON_PRETTY_PRINT|JSON_FORCE_OBJECT);

        exit;
    }

    public static function error(
        string $message = 'Error',
        int $status = 400,
        array $errors = [],
        array $meta = []
    ) {
        http_response_code($status);

        echo json_encode([
            'status'  => 'error',
            'message' => $message,
            'errors'  => $errors,
            'meta'    => $meta,
        ], JSON_PRETTY_PRINT|JSON_FORCE_OBJECT);

        exit;
    }
}

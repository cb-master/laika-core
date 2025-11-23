<?php
/**
 * Laika PHP MVC Framework
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 * License: MIT
 * This file is part of the Laika PHP MVC Framework.
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

// Deny Direct Access
if (php_sapi_name() !== 'cli' && !defined('APP_PATH')) {
    http_response_code(403);
    exit('Direct Access Denied!');
}

use Laika\Core\Http\Request;

######################################################################
/*------------------------ REQUEST FILTERS -------------------------*/
######################################################################
// Get Request Header
add_hook('request.header', function(string $key): ?string {
    return Request::instance()->header($key);
});

// Get Request Input Value
add_hook('request.input', function(string $key, mixed $default = ''): mixed {
    return Request::instance()->input($key, $default);
});

// Get Request Values
add_hook('request.all', function(): array {
    return Request::instance()->all();
});

// Check Method Request is Post/Get/Ajax
add_hook('request.is', function(string $method): bool {
    $method = strtolower($method);
    switch ($method) {
        case 'post':
            return Request::instance()->isPost();
            break;
        case 'get':
            return Request::instance()->isGet();
            break;
        case 'put':
            return Request::instance()->isPut();
            break;
        case 'patch':
            return Request::instance()->isPatch();
            break;
        case 'delete':
            return Request::instance()->isDelete();
            break;
        case 'ajax':
            return Request::instance()->isAjax();
            break;
        default:
            return false;
            break;
    }
});

/**
 * Get Request Error
 * @return string
 */
add_hook('request.error', function(string $key): string{
    $errors = Request::instance()->errors();
    return $errors[$key][0] ?? '';
});
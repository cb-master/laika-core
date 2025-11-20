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

/**
 * App Config
 * @param ?string $key Config Key. Optional Argument. Example: name, version etc.
 * @param mixed $default Default Value if no value found. Optional Argument.
 */
add_filter('config.app', function(?string $key = null, mixed $default = null): mixed{
    return config('app', $key, $default);
});

/**
 * Env Config
 * @param ?string $key Config Key. Optional Argument. Example: name, version etc.
 * @param mixed $default Default Value if no value found. Optional Argument.
 */
add_filter('config.env', function(?string $key = null, mixed $default = null): mixed{
    return config('env', $key, $default);
});

/**
 * Database Config
 * @param ?string $key Config Key. Optional Argument. Example: name, version etc.
 * @param mixed $default Default Value if no value found. Optional Argument.
 */
add_filter('config.database', function(?string $key = null, mixed $default = null): mixed{
    return config('database', $key, $default);
});

/**
 * Database Config
 * @param ?string $key Config Key. Optional Argument. Example: name, version etc.
 * @param mixed $default Default Value if no value found. Optional Argument.
 */
add_filter('config.memcached', function(?string $key = null, mixed $default = null): mixed{
    return config('memcached', $key, $default);
});

/**
 * Database Config
 * @param ?string $key Config Key. Optional Argument. Example: name, version etc.
 * @param mixed $default Default Value if no value found. Optional Argument.
 */
add_filter('config.redis', function(?string $key = null, mixed $default = null): mixed{
    return config('redis', $key, $default);
});

/**
 * Database Config
 * @param ?string $key Config Key. Optional Argument. Example: name, version etc.
 * @param mixed $default Default Value if no value found. Optional Argument.
 */
add_filter('config.secret', function(?string $key = 'key', mixed $default = null): mixed{
    return config('secret', $key, $default);
});
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

namespace Laika\Core\Http;

// Deny Direct Access
if (php_sapi_name() !== 'cli' && !defined('APP_PATH')) {
    http_response_code(403);
    exit('Direct Access Denied!');
}

use Laika\Core\{Config, Token, Uri};

class Response
{
    /**
     * Default Headers
     */
    private static array $headers = [
        "Access-Control-Allow-Origin"       =>  "*",
        "Access-Control-Allow-Methods"      =>  "GET, POST",
        "Access-Control-Allow-Headers"      =>  "Authorization, Origin, X-Requested-With, Content-Type, Accept",
        "Access-Control-Allow-Credentials"  =>  "true",
        "X-Powered-By"                      =>  "Laika",
        "X-Frame-Options"                   =>  "sameorigin",
        "Content-Security-Policy"           =>  "frame-ancestors 'self'",
        "Referrer-Policy"                   =>  "origin-when-cross-origin",
        "Cache-Control"                     =>  "no-store, no-cache, must-revalidate",
        "Pragma"                            =>  "no-cache",
        "Expires"                           =>  "0",
    ];

    /**
     * Set HTTP response code
     * @return int
     */
    public static function code(int $code = 200): int
    {
        http_response_code($code);
        return $code;
    }

    /**
     * Set custom "X-Powered-By" header
     * return void
     */
    public static function poweredBy(string $str): void
    {
        header("X-Powered-By: {$str}", true);
    }

    /**
     * Set or overwrite headers
     * @return void
     */
    public static function setHeader(array $headers = []): void
    {
        foreach ($headers as $key => $value) {
            header(trim($key) . ": " . trim((string) $value), true);
        }
    }

    /**
     * Send default headers + framework-specific ones
     * @return void
     */
    public static function register(): void
    {
        $token = new Token();
        $uri = new Uri();
        $customHeaders = [
            "Request-Time"  =>  option('start.time', time()),
            "App-Provider"  =>  Config::get('app', 'provider'),
            "Authorization" =>  $token->generate([
                'uid'       =>  mt_rand(100001, 999999),
                'requestor' =>  $uri->current()
            ])
        ];

        $headers = array_merge(self::$headers, $customHeaders);

        foreach ($headers as $key => $value) {
            header(trim($key) . ": " . trim((string) $value), true);
        }
    }

    /**
     * Get sent response headers
     * @param string|null $key  Header key to fetch (case-insensitive)
     * @return array|string
     */
    public static function get(?string $key = null): array|string
    {
        $val = [];
        foreach (headers_list() as $header) {
            $parts = explode(':', $header, 2);
            $val[strtolower(trim($parts[0]))] = trim($parts[1] ?? '');
        }

        if ($key !== null) {
            return $val[strtolower($key)] ?? '';
        }

        return $val;
    }
}

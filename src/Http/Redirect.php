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

use Laika\Core\Uri;

class Redirect
{
    /**
     * App Host
     * @var string $host
     */
    protected string $host;

    ##################################################################
    /*------------------------- PUBLIC API -------------------------*/
    ##################################################################

    /**
     * Initiate Object
     */
    public function __construct()
    {
        $uri = new Uri();
        $this->host =   apply_filter('app.host', $uri->base());
    }

    /**
     * Get Method
     * @return string
     * @return never
     */
    public function back(int $code = 302): never
    {
        $this->send($_SERVER['HTTP_REFERER'] ?? $this->host, $code);
    }

    /**
     * Get Method
     * @return never
     */
    public function to(string $to, int $code = 302): never
    {
        $url = parse_url($to, PHP_URL_HOST);
        if (!$url) {
            $to = $this->host . trim($to, '/');
        }
        $this->send($to, $code);
    }

    ####################################################################
    /*------------------------- INTERNAL API -------------------------*/
    ####################################################################

    /**
     * Redirect
     * @return never
     */
    private function send(?string $to = null, int $code = 302): never
    {
        header("Location:{$to}", true, $code);
        exit();
    }

}

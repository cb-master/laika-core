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
     * @property ?self $instance
     */
    protected static ?self $instance = null;

    /**
     * App Host
     * @var string $host
     */
    protected string $host;

    /**
     * Redirect Url
     * @var string $to
     */
    protected string $to;

    /**
     * Response Code
     * @var int $code
     */
    protected int $code;


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
        $this->to   =   $_SERVER['HTTP_REFERER'] ?? $this->host;
        $this->code =   302;
    }

    /**
     * Get Request Instance
     * @return static
     */
    public static function getInstance(): static
    {
        self::$instance ??= new self();
        return self::$instance;
    }

    /**
     * Get Method
     * @return string
     * @return never
     */
    public function back(?int $code = null): never
    {
        $this->code = $code ?: $this->code;
        $this->send();
    }

    /**
     * Get Method
     * @return never
     */
    public function to(string $to, ?int $code = null): never
    {
        $url = parse_url($to, PHP_URL_HOST);
        if ($url) {
            $this->to = $to;
        } else {
            $this->to = $this->host . trim($to, '/');
        }
        $this->code = $code ?: $this->code;
        $this->send();
    }

    ###################################################################
    /*------------------------- INTERNAL API -------------------------*/
    ###################################################################

    /**
     * Redirect
     * @return never
     */
    private function send(): never
    {
        header('Location:' . $this->to, true, $this->code);
        exit();
    }

}

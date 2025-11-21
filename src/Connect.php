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

namespace Laika\Core;

// Deny Direct Access
if (php_sapi_name() !== 'cli' && !defined('APP_PATH')) {
    http_response_code(403);
    exit('Direct Access Denied!');
}

use Laika\Model\ConnectionManager;
use Laika\Session\SessionManager;
use RuntimeException;

class Connect
{
    /**
     * Database Connection
     * @return void
     */
    public static function db(): void
    {
        try {
            $configs = Config::get('database', default:[]);
            foreach ($configs as $name => $config) {
                ConnectionManager::add($config, $name);
            }
        } catch (\Throwable $th) {}
        return;
    }

    /**
     * Set Time Zone
     * @return void
     */
    public static function timezone(): void
    {
        // Set Date Time
        date_default_timezone_set(option('time.zone', 'Europe/London'));
    }

    /**
     * Set Session
     * @return void
     */
    public static function session(): void
    {
        if (option_as_bool('dbsession', false)) {
            SessionManager::config(ConnectionManager::get());
            return;
        }
        SessionManager::config();
        return;
    }
}

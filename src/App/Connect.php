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

namespace Laika\Core\App;

// Deny Direct Access
if (php_sapi_name() !== 'cli' && !defined('APP_PATH')) {
    http_response_code(403);
    exit('Direct Access Denied!');
}

use Laika\Model\ConnectionManager;
use Laika\Session\SessionManager;
use Laika\Core\Config;

class Connect
{
    /**
     * Configure Started
     * @var bool $started
     */
    private static bool $started = false;

    /**
     * App Connect Start
     * @return void
     */
    public static function start(): void
    {
        if (!self::$started) {
            self::$started = true;
            self::setDb();
            self::setTimezone();
            self::setSession();
        }
    }

    /**
     * Database Connection
     * @return void
     */
    private static function setDb(): void
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
    private static function setTimezone(): void
    {
        // Set Date Time
        date_default_timezone_set(option('time.zone', 'Europe/London'));
    }

    /**
     * Set Session
     * @return void
     */
    private static function setSession(): void
    {
        $session_config = [];
        $dbsession = option('dbsession', 'no');
        if ($dbsession == 'yes') {
            $session_config = ConnectionManager::has('default') ? ConnectionManager::get() : [];
        }
        SessionManager::config($session_config);
        return;
    }
}

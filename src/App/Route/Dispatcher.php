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

namespace Laika\Core\App\Route;

use Laika\Core\Http\Response;
use Laika\Core\App\Connect;
use Laika\Core\App\Env;
use Laika\Core\Local;
use Laika\Core\Token;
use RuntimeException;
use Laika\Core\Uri;

class Dispatcher
{
    public static function dispatch(): void
    {
        // Set App Info Environment
        Env::set('app|info', apply_filter('config.app'));

        // Create Required Directories
        self::createDirectories();

        // Get Request Url
        $uri = new Uri();
        $requestUrl = Url::normalize($uri->path());

        // Get If Request Uri Matched With Router List
        $res = Url::matchRequestRoute($requestUrl);

        // Get Parameters
        $params = $res['params'];

        // Load Local
        array_key_exists($uri->segment(1), Handler::getGroups()) ? Local::load($uri->segment(1)) : Local::load();

        // Execute Fallback For Invalid Route
        if ($res['route'] === null) {

            // 404 Response
            http_response_code(404);

            $fallbacks = Handler::getFallbacks();

            foreach (array_reverse($fallbacks) as $key => $callable){
                if (str_starts_with(Url::normalizeFallbackKey($requestUrl), $key)) {
                    try {
                        echo Invoke::controller($callable, $params);
                    } catch (\Throwable $e) {
                        report_bug($e);
                    }
                    // echo Invoke::controller($callable, $params);
                    return;
                }
            }
            /*---- Execute Fallback ----*/
            try {
                echo _404::show();
            } catch (\Throwable $e) {
                report_bug($e);
            }
            // echo _404::show();
            return;
        }

        // App Connect
        if (!str_starts_with($res['route'] ?? '', '/resource')) {
            self::connect();
        }
        
        $routes = Handler::getRoutes(Url::method());
        $route = $routes[$res['route']];

        // Collect before middlewares in order
        $middlewares = array_merge(
            $route['middlewares']['global'],
            $route['middlewares']['group'],
            $route['middlewares']['route']
        );

        // Run Middlewares -> Controller
        try {
            $response = Invoke::middleware($middlewares, $route['controller'], $params);
        } catch (\Throwable $e) {
            report_bug($e);
        }

        // Run Afterware
        $afterwares = array_merge(
            $route['afterwares']['global'],
            $route['afterwares']['group'],
            $route['afterwares']['route']
        );

        try {
            echo empty($afterwares) ? $response : Invoke::afterware($afterwares, $response, $params);
        } catch (\Throwable $e) {
            report_bug($e);
        }
        return;
    }

    /**
     * Connect App
     * @return void
     */
    private static function connect(): void
    {
        // Connect App DB & Session
        Connect::start();
        // Set Headers
        $token = new Token();
        $uri = new Uri();
        Response::instance()->setHeader([
            "Request-Time"  =>  apply_filter('config.app', 'start.time', time()),
            "App-Provider"  =>  apply_filter('config.app', 'name', 'Laika Framework'),
            "Authorization" =>  $token->generate([
                'uid'       =>  mt_rand(100001, 999999),
                'requestor' =>  $uri->base()
            ])
        ]);
        return;
    }

    /**
     * Create Required Directories
     * @return void
     */
    private static function createDirectories(): void
    {
        $dirs = [
            APP_PATH . '/Controller',
            APP_PATH . '/Model',
            APP_PATH . '/Middleware',
            APP_PATH . '/Afterware',
        ];

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                if (!mkdir($dir, 0755, true)) {
                    throw new RuntimeException("Failed to Create Directory: {$dir}");
                }
            }
        }
        return;
    }
}

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

use Laika\Core\{Config, Uri, Local, Token, App\Env, App\Connect, Http\Response};

class Dispatcher
{
    public static function dispatch(): void
    {
        // Set App Info Environment
        Env::set('app|info', Config::get('app'));

        // Get Request Url
        $uri = new Uri();
        $requestUrl = Url::normalize($uri->path());

        // Get If Request Uri Matched With Router List
        $res = Url::matchRequestRoute($requestUrl);

        // Get Parameters
        $params = $res['params'];

        // Load Local
        array_key_exists($uri->segment(1), Handler::getGroups()) ? Local::load($uri->segment(1)) : Local::load();

        // App Connect
        if (!str_starts_with($res['route'] ?? '', '/resource')) {
            self::connect();
        }
        // Execute Fallback For Invalid Route
        if ($res['route'] === null) {

            // 404 Response
            http_response_code(404);

            $fallbacks = Handler::getFallbacks();

            foreach (array_reverse($fallbacks) as $key => $callable){
                if (str_starts_with(Url::normalizeFallbackKey($requestUrl), $key)) {
                    echo Invoke::controller($callable, $params);
                    return;
                }
            }
            /*---- Execute Fallback ----*/
            echo _404::show();
            return;
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
        $response = Invoke::middleware($middlewares, $route['controller'], $params);

        // Run Afterware
        $afterwares = array_merge(
            $route['afterwares']['global'],
            $route['afterwares']['group'],
            $route['afterwares']['route']
        );

        echo empty($afterwares) ? $response : Invoke::afterware($afterwares, $response, $params);
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
        Response::setHeader([
            "Request-Time"  =>  option('start.time', time()),
            "App-Provider"  =>  Config::get('app', 'name', 'Laika Framework'),
            "Authorization" =>  $token->generate([
                'uid'       =>  mt_rand(100001, 999999),
                'requestor' =>  $uri->base()
            ])
        ]);
        return;
    }
}

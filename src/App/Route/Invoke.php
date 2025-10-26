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

use RuntimeException;

class Invoke
{
    /**
     * Invoke Middleware
     * @param array $middlewares Middlewares to Invoke
     * @param callable|string|array|null|object $controller Controller to Call After Middlewares Run
     * @param array $params Parameters
     * @return ?string Return Response From Controller
     */
    public static function middleware(array $middlewares, callable|string|array|null|object $controller, array $params = []): ?string
    {
        // Build the Chain in Normal Order (global → group → route)
        $next = array_reduce(
            array_reverse($middlewares), // Reverse to Preserve Execution order
            function ($next, $middleware) use ($params) {
                return function ($params) use ($middleware, $next) {
                    // Separate Parameters From Middleware
                    $parts = explode('|', $middleware);
                    $parts[0] = trim($parts[0], '\\');
                    $middleware = "Laika\\App\\Middleware\\$parts[0]";
                    if (isset($parts[1])) {
                        $args = [];
                        $paramParts = explode(',', $parts[1]);
                        foreach ($paramParts as $paramPart) {
                            [$k, $v] = explode('=', $paramPart);
                            $args[trim($k)] = trim($v);
                        }
                        $params = array_merge($params, $args);
                    }
                    if (!class_exists($middleware)) {
                        throw new \Exception("Invalid middleware: {$middleware}");
                    }
                    $obj = new $middleware;

                    // Check handle Method Exists
                    if (!method_exists($obj, 'handle')) {
                        throw new \Exception("'handle' Method Doesn't Exists in {$middleware}");
                    }
                    return $obj->handle($next, $params);
                };
            },
            // Final callable (controller)
            function ($params) use ($controller) {
                return self::controller($controller, $params);
            }
        );

        // Execute the full chain
        return $next($params);
    }

    /**
     * Invoke Afterware
     * @param array $afterwares Afterwares to Invoke
     * @param ?string $response Rresponse to Show
     * @param array $params Parameters
     * @return ?string Return Response From Controller
     */
    public static function afterware(array $afterwares, ?string $response, array $params = []): ?string
    {
        // Build the Chain in Normal Order (global → group → route)
        $next = array_reduce(
            array_reverse($afterwares), // Reverse to Preserve Execution order
            function ($next, $afterware) use ($params) {
                return function ($response) use ($afterware, $next, $params) {
                    // Separate Parameters From Afterware
                    $parts = explode('|', $afterware);
                    $parts[0] = trim($parts[0], '\\');
                    $afterware = "Laika\\App\\Middleware\\$parts[0]";
                    if (isset($parts[1])) {
                        $args = [];
                        $paramParts = explode(',', $parts[1]);
                        foreach ($paramParts as $paramPart) {
                            [$k, $v] = explode('=', $paramPart);
                            $args[trim($k)] = trim($v);
                        }
                        $params = array_merge($params, $args);
                    }
                    if (!class_exists($afterware)) {
                        throw new \RuntimeException("Invalid Afterware: {$afterware}");
                    }

                    $obj = new $afterware;

                    if (!method_exists($obj, 'terminate')) {
                        throw new \RuntimeException("'terminate' Method Doesn't Exist in {$afterware}");
                    }

                    // Execute the current afterware, passing response and chain
                    return $obj->terminate($response, function ($newResponse) use ($next, $params) {
                        return $next($newResponse);
                    }, $params);
                };
            },
            fn($response) => $response // initial chain returns final response
        );

        return $next($response);
    }

    public static function controller(callable|string|array|null|object $handler, array $args): ?string
    {
        if (is_null($handler)) {
            return null;
        }

        if (is_callable($handler)) {
            $reflection = new Reflection($handler, $args);
            return call_user_func($handler, ...$reflection->namedArgs());
        }

        if (is_array($handler)) {
            [$controller, $method] = $handler;
            // Check Controller Exists
            if (!class_exists($controller)) {
                throw new RuntimeException("Controller '{$controller}' Doesn't Exists");
            }
            // Check Method Exists
            if (!method_exists($controller, $method)) {
                throw new RuntimeException("Controller Method '{$method}' Doesn't Exists");
            }
            // Call Controller
            $obj = new $controller();
            $reflection = new Reflection([$obj, $method], $args);
            return call_user_func([$obj, $method], ...$reflection->namedArgs());
        }

        if (is_object($handler)) {
            // Check Method Exists
            if (!method_exists($handler, 'index')) {
                throw new RuntimeException("Method 'index' Doesn't Exists in Controller " . get_class($handler));
            }
            // Call Controller
            $reflection = new Reflection([$handler, 'index'], $args);
            return call_user_func([$handler, 'index'], ...$reflection->namedArgs());
        }

        if (is_string($handler)) {
            $parts = explode('@', $handler);
            $controller = $parts[0];
            if (!isset($parts[1])) {
                throw new RuntimeException("'{$handler}' Containes Invalid Method!");
            }
            [$controller, $method] = explode('@', $handler);
            $controller = "Laika\\App\\Controller\\{$controller}";
            // Check Controller Exists
            if (!class_exists($controller)) {
                throw new RuntimeException("Controller '{$controller}' Doesn't Exists");
            }
            // Check Method Exists
            if (!method_exists($controller, $method)) {
                throw new RuntimeException("Method '{$method}' Doesn't Exists in Controller '{$controller}'");
            }
            // Call Controller
            $obj = new $controller();
            $reflection = new Reflection([$controller, $method], $args);
            return call_user_func([$obj, $method], ...$reflection->namedArgs());
        }

        throw new RuntimeException("Invalid Controller: " . print_r($handler, true));
    }
}

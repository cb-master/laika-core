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

use Laika\Core\{Language, Cookie, Config};

####################################################################
/*------------------------- APP FILTERS --------------------------*/
####################################################################
// App Host
add_filter('app.host', function(): string
{
    return rtrim(host(), '/') . '/';
});

// App Name
add_filter('app.name', function(){
    return option('app.name') ?: Config::get('app', ) ?: 'Laika Framework!';
});

/**
 * App Logo
 * @param ?string $option_key opt_ken column value in Database options Table
 * @return string
 */
add_filter('app.logo', function(?string $option_key = null): string {
    $name = option($option_key ?? '') ?: null;
    $logo = $name ?: 'logo.png';
    return apply_filter('app.host') . "resource/img/{$logo}";
});

/**
 * App Icon
 * @param ?string $option_key opt_ken column value in Database options Table
 * @return string
 */
add_filter('app.icon', function(?string $option_key = null): string {
    $name = option($option_key) ?: null;
    $icon = $name ?: 'favicon.ico';
    return apply_filter('app.host') . "resource/img/{$icon}";
});

/**
 * Local Language
 * @param string $property Property of LANG Class
 * @param array ...$args Other Parameters for sprintf()
 * @return string
 */
add_filter('app.local', function(string $property, ...$args): string {
    // Return if Class Doesn't Exists
    if(!class_exists('LANG')) throw new RuntimeException("'LANG' Class Doesn't Exists!");
    // Return if Class Exists
    $value = LANG::$$property ?? 'Local Property Does Not Exists!';
    return $value ? sprintf($value, ...$args) : '';
});
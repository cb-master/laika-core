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

use Laika\Core\Http\Response;

// Deny Direct Access
if (php_sapi_name() !== 'cli' && !defined('APP_PATH')) {
    http_response_code(403);
    exit('Direct Access Denied!');
}

#####################################################################
/*----------------------- TEMPLATE FILTERS ------------------------*/
#####################################################################
// Load Asset
add_filter('template.asset', function(string $file): string {
    if(parse_url($file, PHP_URL_HOST)){
        return $file;
    }
    $file = trim($file, '/');
    return apply_filter('app.host') . "resource/{$file}";
});

// Set Template Default JS Vars
add_filter('template.js.vars', function(): string{
    $authorizarion = Response::instance()->get('authorization');
    $appuri = trim(apply_filter('app.host'), '/');
    $timeformat = option('time.format', 'Y-M-d H:i:s');
    return <<<HTML
        <script>
                let token = '{$authorizarion}';
                let appuri = '{$appuri}';
                let timeformat = '{$timeformat}';
            </script>\n
    HTML;
});
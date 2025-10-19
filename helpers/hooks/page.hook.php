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

use Laika\Core\Uri;

####################################################################
/*------------------------- PAGE FILTERS -------------------------*/
####################################################################
// Page Title
add_filter('page.title', function(string $title): string {
    return "{$title} | " . apply_filter('app.name');
});

// Page Number
add_filter('page.number', function(): int {
    $number = (int) apply_filter('request.input', 'page', 1);
    return $number < 1 ? 1 : $number;
});

// Next Page Number
add_filter('page.next', function()
{
    $uri = new Uri();
    return $uri->incrementQuery();
});

// Previous Page Number
add_filter('page.previous', function()
{
    $uri = new Uri();
    return $uri->decrementQuery();
});
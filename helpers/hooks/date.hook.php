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

use Laika\Core\Date;

#####################################################################
/*------------------------- DATE FILTERS --------------------------*/
#####################################################################
/**
 * Display Date
 * @param int $time Unix Timestamps
 * @return string
 */
add_filter('date.show', function(int $time): string{
    $date = new Date();
    $date->setTimestamp($time);
    return $date->format();
});
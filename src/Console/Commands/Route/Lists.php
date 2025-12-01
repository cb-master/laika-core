<?php

/**
 * Laika Framework
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 * License: MIT
 * This file is part of the Laika PHP MMC Framework.
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Laika\Core\Console\Commands\Route;

use Laika\Core\Console\Command;
use Laika\Core\App\Router;

// Make Controller Class
class Lists extends Command
{
    // App Controller Path
    protected string $path = APP_PATH . '/lf-routes';

    /**
     * @param array $params
     * @return void
     */
    public function run(array $params): void
    {
        echo <<<PHP
        -------------------------------------------------------------------
        REGISTERED ROUTES:
        -------------------------------------------------------------------\n
        PHP;
        // Get Http List
        Router::inspectAll();
        return;
    }
}

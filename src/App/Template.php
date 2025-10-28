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

use Laika\Template\Template as Engine;
use Laika\Core\Directory;

class Template extends Engine
{
    /**
     * @var ?string $templateDirectory Template Directory
     */
    protected ?string $templateDirectory;

    /**
     * @var ?string $cacheDirectory Template Cache Directory
     */
    protected ?string $cacheDirectory;

    public function __construct(?string $templateSubDirectory = null, ?string $cacheSubDirectory = null)
    {
        // Make Template Direcory
        $templateSubDirectory = $templateSubDirectory ? '/' . trim($templateSubDirectory, '/') : '';
        $this->templateDirectory = APP_PATH . "/lf-templates{$templateSubDirectory}";
        Directory::make($this->templateDirectory);

        // Make Template Cache Direcory
        $cacheSubDirectory = $cacheSubDirectory ? '/' . trim($cacheSubDirectory, '/') : '';
        $this->cacheDirectory = APP_PATH . "/lf-cache{$cacheSubDirectory}";
        Directory::make($this->cacheDirectory);

        // Run Template Engine
        parent::__construct($this->templateDirectory, $this->cacheDirectory);
        $this->assign('app_info', Env::get('app|info'));
    }

    public function view(string $name): string
    {
        return $this->render($name);
    }
}

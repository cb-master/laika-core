<?php

/**
 * Laika PHP MVC Framework
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 * License: MIT
 * This file is part of the Laika PHP MVC Framework.
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Laika\Core;

// Deny Direct Access
if (php_sapi_name() !== 'cli' && !defined('APP_PATH')) {
    http_response_code(403);
    exit('Direct Access Denied!');
}

class Page
{
    /**
     * @var ?object $instance
     */
    private static ?object $instance;

    /**
     * Total Results
     * @var int $totalResults
     */
    private int $totalResults;

    /**
     * Total Pages
     * @var int $totalPages
     */
    private int $totalPages;

    ##################################################################
    /* ------------------------ PUBLIC API ------------------------ */
    ##################################################################
    /**
     * Total Results
     * @param ?int $totalResults Default is null
     */
    public function __construct(?int $totalResults = null)
    {
        $this->totalResults = (int) $totalResults < 1  ? 1 : (int) $totalResults;
        $this->totalPages = (int) ceil($this->totalResults / (int) option('data.limit', 20));
    }

    /**
     * Singleton Instance
     * @param ?int $totalResults Default is null
     * @return self
     */
    public static function instance(?int $totalResults = null): self
    {
        self::$instance ??= new self($totalResults);
        return self::$instance;
    }

    /**
     * Total Pages Exists
     * @return int
     */
    public function pages(): int
    {
        return $this->totalPages;
    }

    /**
     * Total Pages Results
     * @param int $totalResults
     * @return int
     */
    public function total(): int
    {
        return $this->totalResults;
    }

    /**
     * Next Page Url
     * @return string
     */
    public function next(): string
    {
        return Uri::instance()->incrementQuery();
    }

    /**
     * Previous Page Url
     * @return string
     */
    public function previous()
    {
        return Uri::instance()->decrementQuery();
    }
}

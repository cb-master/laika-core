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

namespace Laika\Core;

use Laika\Model\ConnectionManager;
use Laika\Session\Session;
use PDO;

class Auth
{
    // Session For
    private string $for;

    // PDO Object
    private PDO $pdo;

    // DB Table Name
    private string $table;

    // Cookie Name
    private string $cookie = '__AUTH';

    // Cookie Expire After TTL
    private int $ttl = 1800; // 1800 Seconds or 30 Minutes

    // User Data
    private ?array $user = null;

    // Event ID
    private ?string $event;

    // Real Time
    private int $time;

    /**
     * Initiate Auth Session
     * @param string $for. Auth Running For. Example: ADMIN/CLIENT. Default is APP
     */
    public function __construct(string $for = 'APP')
    {
        $this->for = strtolower($for);
        $this->pdo = ConnectionManager::get();
        $this->table = "{$this->for}_sessions";
        $this->event = Session::get($this->cookie, $this->for);
        $this->time = (int) option('start.time', time());
    }

    /**
     * Checkng TTL
     * @param int $ttl Required TTL Numer. Sytem Default is 1800 Seconds or 30 Minutes
     * @return void
     */
    public function setTtl(int $ttl): void
    {
        $this->ttl = $ttl;
    }

    /**
     * Create Auth Token in DB Table
     * @param array $user User Data
     * @return string Event ID
     */
    public function create(array $user): string
    {
        $this->user = $user;

        // Get Event ID
        $this->event = bin2hex(random_bytes(32));
        // Set Expire Time
        $expire = $this->time + $this->ttl;
        // Make SQL
        $sql = "INSERT INTO {$this->table} (event, data, expire, created) VALUES (:event, :data, :expire, :created)";
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':event'    =>  $this->event,
            ':data'     =>  json_encode($user),
            ':expire'   =>  $expire,
            ':created'  =>  $this->time,
        ]);

        // Set Session
        Session::set($this->cookie, $this->event, $this->for);

        return $this->event;
    }

    /**
     * Get User Data
     * Check User is Authenticated and Not Expired
     * @return ?array
     */
    public function user(): ?array
    {
        // Clear Session if Event Mssing
        if (empty($this->event)) {
            Session::pop($this->cookie, $this->for);
            return null;
        }

        // Get DB Data
        $stmt = $this->pdo->prepare("SELECT data, expire FROM {$this->table} WHERE event = :event AND expire > :expire LIMIT 1");
        $stmt->execute([':event' => $this->event, ':expire' => $this->time]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            Session::pop($this->cookie, $this->for);
            return null;
        }

        $this->user = json_decode($row['data'], true);

        if (($row['expire'] - $this->time) < ($this->ttl / 2)) {
            self::regenerate();
        }

        return $this->user;
    }

    /**
     * Regenerate Auth Event ID
     * @return string
     */
    public function regenerate(): string
    {
        $this->destroy();
        return $this->create($this->user);
    }

    /**
     * Destroy Auth Event ID
     * @return void
     */
    public function destroy(): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE event = :event");
        $stmt->execute([':event' => $this->event]);
        Session::pop($this->cookie, $this->for);
    }
}

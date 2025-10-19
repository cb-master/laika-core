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

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Laika\Core\Config;
use Exception;

class Token
{
    /**
     * Secret Key
     * @var string $secret
     */
    private string $secret;

    /**
     * Token Issuer
     * @var string $issuer
     */
    private string $issuer;

    /**
     * Token Audience
     * @var string $audience
     */
    private string $audience;

    /**
     * Algorithm
     * @var string $algorithm
     */
    private string $algorithm = 'HS256';

    /**
     * Token Expire Time
     * @var int $expiration Default 1 hour
     */
    private int $expiration = 3600;

    /**
     * User Data
     * @var array $currentUser
     */
    private ?array $currentUser = null;

    /**
     * @param string $secret Required Argument. 256 bit Secret Key
     * @param ?int $expiration Optional Argument. Example 1800 for 30 Minutes
     */
    public function __construct(?int $expiration = null)
    {
        $uri = new Uri();
        $this->secret = Config::get('secret', 'key');
        $this->issuer = $uri->host();
        $this->audience = $uri->host();
        $this->expiration = $expiration ?: $this->expiration;
    }

    /**
     * Register
     * @param array $user Requried Argument. Example ['id'=>1,'type'=>'staff']
     * @return string
     */
    public function generate(?array $user = null): string
    {
        // $now = new DateTimeImmutable();
        $time = new Date();
        $payload = [
            'iss'   =>  $this->issuer,
            'aud'   =>  $this->audience,
            'iat'   =>  $time->getTimestamp(),
            'nbf'   =>  $time->getTimestamp(),
            'exp'   =>  $time->getTimestamp() + $this->expiration,
            'data'  =>  $user
        ];

        return JWT::encode($payload, $this->secret, $this->algorithm);
    }

    /**
     * Validate Token
     * @param ?string $token Required Argument. Example: JWT Encoded Token
     * @return bool
     */
    public function validateToken(?string $token): bool
    {
        try {
            $decoded = JWT::decode($token ?: '', new Key($this->secret, $this->algorithm));
            $this->currentUser = (array) $decoded->data;
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check User Data Exist
     * @return bool
     */
    public function check(): bool
    {
        return $this->currentUser ? true : false;
    }

    /**
     * Get User Data
     * Run validateToken() First
     * @return ?array
     */
    public function user(): ?array
    {
        return $this->currentUser;
    }

    /**
     * Flush User Data
     * @return void
     */
    public function flush(): void
    {
        $this->currentUser = null;
    }

    /**
     * Refresh JWT Token With New Expired Time
     * @return ?string
     */
    public function refresh(string $token): ?string
    {
        if (!$this->validateToken($token)) {
            return null;
        }
        return $this->generate($this->currentUser);
    }
}

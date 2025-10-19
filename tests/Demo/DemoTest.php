<?php
declare(strict_types=1);

namespace Laika\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Laika\Core\App\Router;
use Laika\Core\Http\Request;
use Laika\Core\Date;

final class DemoTest extends TestCase
{
    public function testRouter()
    {
        Router::get('/', function() {
            return 'Hello, World!';
        })->name('home');
        $path = Router::url('home');
        $this->assertNotNull($path ?: null, "Failed to Initialize Router or Generate URL");
    }

    public function testDate(): void
    {
        $date = new Date('2024-01-01');
        $this->assertNotNull($date->getTimeStamp(), "Failed to Initialize Date or Get Timestamp");
    }

    public function testRequest(): void
    {
        $this->assertTrue(Request::isGet(), "Failed to Detect GET Request");
    }
}
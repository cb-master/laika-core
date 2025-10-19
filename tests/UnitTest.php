<?php
namespace Laika\Tests;

use PHPUnit\Framework\TestCase;
use Laika\Core\App\Router;
class UnitTest extends TestCase
{    
    public function testRenderSimple()
    {
        Router::get('/', function() {
            return 'Hello, World!';
        })->name('home');
        $path = Router::url('home');
        $this->assertNotNull($path ?: null, "Failed to Initialize Router or Generate URL");
    }
}
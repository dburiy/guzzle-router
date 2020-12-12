<?php

use PHPUnit\Framework\TestCase;
use Dburiy\Router\Router;

class RouterTest extends TestCase
{
    /** @var Router */
    private static $router;

    public static function setUpBeforeClass(): void
    {
        self::$router = new Router([
            'timeout' => 3,
            'verify' => false
        ]);
    }

    public function testInit()
    {
        $this->assertSame('GuzzleHttp\Client', get_class(self::$router->getClient()));
    }

    public function testGetRoutes()
    {
        $routes = self::$router->getRoutes();
        $this->assertIsArray($routes);
        $this->assertCount(0, $routes);
    }

    public function testRoutes()
    {
        self::$router->get('test1', 'https://localhost');
        $this->assertTrue(self::$router->hasRoute('test1'));

        self::$router->post('test2', 'https://localhost');
        $this->assertTrue(self::$router->hasRoute('test2'));

        self::$router->removeRoute('test1');
        $this->assertTrue(!self::$router->hasRoute('test1'));

        self::$router->cleanRoutes();
        $this->assertCount(0, self::$router->getRoutes());
    }

    public function testCall()
    {
        self::$router->get('test', 'https://ya.ru');
        $response = self::$router->call('test');
        $this->assertSame('GuzzleHttp\Psr7\Response', get_class($response));
    }
}
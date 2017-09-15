<?php

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Itval\core\Classes\Router;

require_once 'components.php';

class RouterTest extends TestCase
{

    public function testWithValidUrl()
    {
        $request = new ServerRequest('GET', '/contact');
        $router = new Router($request);
        $response = $router->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testWithoutValidUrl()
    {
        $request = new ServerRequest('GET', '/contract');
        $router = new Router($request);
        $response = $router->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }
}

<?php

namespace Tests\Php7\Handlers;

use Error;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Illuminate\Container\Container;
use LaravelBridge\Slim\App;
use LaravelBridge\Slim\Handlers\PhpError;
use PHPUnit\Framework\TestCase;

class PhpErrorTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetSymfonyResponseWhenCatchException()
    {
        $container = (new App(new Container()))->getContainer();
        $target = new PhpError($container);

        $mockRequest = new ServerRequest('GET', '/');

        $response = $target($mockRequest, new Response(), new Error);

        $this->assertSame(500, $response->getStatusCode());
        $this->assertContains('Whoops, looks like something went wrong.', (string)$response->getBody());
    }

    /**
     * @test
     */
    public function shouldGetJsonResponseWhenRequestExpectJson()
    {
        $container = (new App(new Container()))->getContainer();
        $target = new PhpError($container);

        $mockRequest = new ServerRequest('GET', '/', [
            'ACCEPT' => 'application/json',
        ]);

        $response = $target($mockRequest, new Response(), new Error);

        $this->assertSame(500, $response->getStatusCode());
        $this->assertJson((string)$response->getBody());
    }
}

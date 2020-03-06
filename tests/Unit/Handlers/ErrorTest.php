<?php

namespace Tests\Unit\Handlers;

use Exception;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Illuminate\Container\Container;
use LaravelBridge\Slim\App;
use LaravelBridge\Slim\Handlers\Error;
use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetSymfonyResponseWhenCatchException(): void
    {
        $container = (new App(new Container()))->getContainer();
        $target = new Error($container);

        $mockRequest = new ServerRequest('GET', '/');

        $response = $target($mockRequest, new Response(), new Exception());

        $this->assertSame(500, $response->getStatusCode());
        $this->assertContains('Whoops, looks like something went wrong.', (string)$response->getBody());
    }

    /**
     * @test
     */
    public function shouldGetJsonResponseWhenRequestExpectJson()
    {
        $container = (new App(new Container()))->getContainer();
        $target = new Error($container);

        $mockRequest = new ServerRequest('GET', '/', [
            'ACCEPT' => 'application/json',
        ]);

        $response = $target($mockRequest, new Response(), new Exception());

        $this->assertSame(500, $response->getStatusCode());
        $this->assertJson((string)$response->getBody());
    }
}

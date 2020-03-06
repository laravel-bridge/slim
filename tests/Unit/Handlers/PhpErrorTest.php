<?php

namespace Tests\Php7\Handlers;

use Error;
use Illuminate\Container\Container;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use LaravelBridge\Slim\App;
use LaravelBridge\Slim\Handlers\PhpError;
use PHPUnit\Framework\TestCase;

class PhpErrorTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetSymfonyResponseWhenCatchException(): void
    {
        $container = (new App(new Container()))->getContainer();
        $target = new PhpError($container);

        $mockRequest = new ServerRequest([], [], '/', 'GET');

        $response = $target($mockRequest, new Response(), new Error());

        $this->assertSame(500, $response->getStatusCode());
        $this->assertStringContainsString('Whoops, looks like something went wrong.', (string)$response->getBody());
    }

    /**
     * @test
     */
    public function shouldGetJsonResponseWhenRequestExpectJson(): void
    {
        $container = (new App(new Container()))->getContainer();
        $target = new PhpError($container);

        $mockRequest = new ServerRequest([], [], '/', 'GET');
        $mockRequest = $mockRequest->withHeader('ACCEPT', 'application/json');

        $response = $target($mockRequest, new Response(), new Error());

        $this->assertSame(500, $response->getStatusCode());
        $this->assertJson((string)$response->getBody());
    }
}

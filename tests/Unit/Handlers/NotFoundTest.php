<?php

namespace Tests\Unit\Handlers;

use Illuminate\Container\Container;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use LaravelBridge\Slim\App;
use LaravelBridge\Slim\Handlers\NotFound;
use PHPUnit\Framework\TestCase;

class NotFoundTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetSymfonyResponseWhenNotFound(): void
    {
        $container = (new App(new Container()))->getContainer();
        $target = new NotFound($container);

        $mockRequest = new ServerRequest([], [], '/whatever', 'GET');

        $response = $target($mockRequest, new Response());
        $body = (string)$response->getBody();

        $this->assertSame(404, $response->getStatusCode());
        $this->assertStringContainsString('Sorry, the page you are looking for could not be found.', $body);
    }

    /**
     * @test
     */
    public function shouldGetJsonResponseWhenRequestExpectJson()
    {
        $container = (new App(new Container()))->getContainer();
        $target = new NotFound($container);

        $mockRequest = new ServerRequest([], [], '/whatever', 'GET');
        $mockRequest = $mockRequest->withHeader('ACCEPT', 'application/json');

        $response = $target($mockRequest, new Response());

        $this->assertSame(404, $response->getStatusCode());
        $this->assertJson((string)$response->getBody());
    }
}

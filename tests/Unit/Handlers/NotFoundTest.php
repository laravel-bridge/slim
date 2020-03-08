<?php

namespace Tests\Unit\Handlers;

use Illuminate\Container\Container;
use LaravelBridge\Slim\App;
use LaravelBridge\Slim\Handlers\NotFound;
use LaravelBridge\Slim\Testing\Concerns\MakesHttpRequests;
use PHPUnit\Framework\TestCase;
use Slim\Http\Response;

class NotFoundTest extends TestCase
{
    use MakesHttpRequests;

    /**
     * @test
     */
    public function shouldGetSymfonyResponseWhenNotFound(): void
    {
        $container = (new App(new Container()))->getContainer();
        $target = new NotFound($container);

        $mockRequest = $this->createServerRequest('GET', '/whatever');

        $response = $target($mockRequest, new Response());
        $body = (string)$response->getBody();

        $this->assertSame(404, $response->getStatusCode());
        $this->assertStringContainsString('Sorry, the page you are looking for could not be found.', $body);
    }

    /**
     * @test
     */
    public function shouldGetJsonResponseWhenRequestExpectJson(): void
    {
        $container = (new App(new Container()))->getContainer();
        $target = new NotFound($container);

        $mockRequest = $this->createServerRequest('GET', '/whatever')
            ->withHeader('ACCEPT', 'application/json');

        $response = $target($mockRequest, new Response());

        $this->assertSame(404, $response->getStatusCode());
        $this->assertJson((string)$response->getBody());
    }
}

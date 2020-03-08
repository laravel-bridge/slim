<?php

namespace Tests\Php7\Handlers;

use Error;
use Illuminate\Container\Container;
use LaravelBridge\Slim\App;
use LaravelBridge\Slim\Handlers\PhpError;
use LaravelBridge\Slim\Testing\Concerns\MakesHttpRequests;
use PHPUnit\Framework\TestCase;
use Slim\Http\Response;

class PhpErrorTest extends TestCase
{
    use MakesHttpRequests;

    /**
     * @test
     */
    public function shouldGetSymfonyResponseWhenCatchException(): void
    {
        $container = (new App(new Container()))->getContainer();
        $target = new PhpError($container);

        $mockRequest = $this->createServerRequest('GET', '/');

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

        $mockRequest = $this->createServerRequest('GET', '/')
            ->withHeader('ACCEPT', 'application/json');

        $response = $target($mockRequest, new Response(), new Error());

        $this->assertSame(500, $response->getStatusCode());
        $this->assertJson((string)$response->getBody());
    }
}

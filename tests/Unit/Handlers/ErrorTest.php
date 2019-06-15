<?php

namespace Tests\Unit\Handlers;

use Exception;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use LaravelBridge\Slim\App;
use LaravelBridge\Slim\Handlers\Error;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelBridge\Laravel;

class ErrorTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetSymfonyResponseWhenCatchException()
    {
        $container = (new App(new Laravel()))->getContainer();
        $target = new Error($container);

        $mockRequest = new ServerRequest('GET', '/');

        $response = $target($mockRequest, new Response(), new Exception());

        $this->assertContains('Whoops, looks like something went wrong.', (string)$response->getBody());
    }

    /**
     * @test
     */
    public function shouldGetJsonResponseWhenRequestExpectJson()
    {
        $container = (new App(new Laravel()))->getContainer();
        $target = new Error($container);

        $mockRequest = new ServerRequest('GET', '/', [
            'ACCEPT' => 'application/json',
        ]);

        $response = $target($mockRequest, new Response(), new Exception());

        $this->assertJson((string)$response->getBody());
    }
}

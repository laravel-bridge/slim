<?php

namespace Tests\Unit\Handlers;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use LaravelBridge\Slim\App;
use LaravelBridge\Slim\Handlers\NotFound;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelBridge\Laravel;

class NotFoundTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetSymfonyResponseWhenNotFound()
    {
        $container = (new App(new Laravel()))->getContainer();
        $target = new NotFound($container);

        $mockRequest = new ServerRequest('GET', '/whatever');

        $response = $target($mockRequest, new Response());

        $this->assertSame(404, $response->getStatusCode());
        $this->assertContains('Sorry, the page you are looking for could not be found.', (string)$response->getBody());
    }

    /**
     * @test
     */
    public function shouldGetJsonResponseWhenRequestExpectJson()
    {
        $container = (new App(new Laravel()))->getContainer();
        $target = new NotFound($container);

        $mockRequest = new ServerRequest('GET', '/whatever', [
            'ACCEPT' => 'application/json',
        ]);

        $response = $target($mockRequest, new Response());

        $this->assertSame(404, $response->getStatusCode());
        $this->assertJson((string)$response->getBody());
    }
}

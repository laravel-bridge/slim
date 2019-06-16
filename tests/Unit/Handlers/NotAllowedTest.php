<?php

namespace Tests\Unit\Handlers;

use Exception;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use LaravelBridge\Slim\App;
use LaravelBridge\Slim\Handlers\NotAllowed;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelBridge\Laravel;

class NotAllowedTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetSymfonyResponseWhenCallByNotAllowedMethod()
    {
        $container = (new App(new Laravel()))->getContainer();
        $container->get('settings')->set('displayErrorDetails', true);

        $target = new NotAllowed($container);

        $mockRequest = new ServerRequest('POST', '/');

        $response = $target($mockRequest, new Response(), ['GET']);

        $this->assertSame(405, $response->getStatusCode());
        $this->assertContains('The POST method is not supported for this route', (string)$response->getBody());
        $this->assertContains('Supported methods: GET', (string)$response->getBody());
    }

    /**
     * @test
     */
    public function shouldGetJsonResponseWhenCallByNotAllowedMethodAndRequestWantsJson()
    {
        $container = (new App(new Laravel()))->getContainer();
        $container->get('settings')->set('displayErrorDetails', true);

        $target = new NotAllowed($container);

        $mockRequest = new ServerRequest('POST', '/', [
            'ACCEPT' => 'application/json',
        ]);

        $response = $target($mockRequest, new Response(), ['GET']);

        $this->assertSame(405, $response->getStatusCode());
        $this->assertJson((string)$response->getBody());
        $this->assertContains('The POST method is not supported for this route', (string)$response->getBody());
        $this->assertContains('Supported methods: GET', (string)$response->getBody());
    }
}

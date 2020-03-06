<?php

namespace Tests\Unit\Handlers;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Illuminate\Container\Container;
use LaravelBridge\Slim\App;
use LaravelBridge\Slim\Handlers\NotAllowed;
use PHPUnit\Framework\TestCase;

class NotAllowedTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetSymfonyResponseWhenCallByNotAllowedMethod(): void
    {
        $container = (new App(new Container()))->getContainer();
        $settings = $container->get('settings');
        $settings['displayErrorDetails'] = true;

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
    public function shouldGetJsonResponseWhenCallByNotAllowedMethodAndRequestWantsJson(): void
    {
        $container = (new App(new Container()))->getContainer();
        $settings = $container->get('settings');
        $settings['displayErrorDetails'] = true;

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

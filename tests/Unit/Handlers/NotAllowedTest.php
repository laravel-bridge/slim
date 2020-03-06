<?php

namespace Tests\Unit\Handlers;

use Illuminate\Container\Container;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
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

        $mockRequest = new ServerRequest([], [], '/', 'POST');

        $response = $target($mockRequest, new Response(), ['GET']);
        $body = (string)$response->getBody();

        $this->assertSame(405, $response->getStatusCode());
        $this->assertStringContainsString('The POST method is not supported for this route', $body);
        $this->assertStringContainsString('Supported methods: GET', $body);
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

        $mockRequest = new ServerRequest([], [], '/', 'POST');
        $mockRequest = $mockRequest->withHeader('ACCEPT', 'application/json');

        $response = $target($mockRequest, new Response(), ['GET']);
        $body = (string)$response->getBody();

        $this->assertSame(405, $response->getStatusCode());
        $this->assertJson($body);
        $this->assertStringContainsString('The POST method is not supported for this route', $body);
        $this->assertStringContainsString('Supported methods: GET', $body);
    }
}

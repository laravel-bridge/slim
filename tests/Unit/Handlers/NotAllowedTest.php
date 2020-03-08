<?php

namespace Tests\Unit\Handlers;

use Illuminate\Container\Container;
use LaravelBridge\Slim\App;
use LaravelBridge\Slim\Handlers\NotAllowed;
use LaravelBridge\Slim\Testing\Concerns\MakesHttpRequests;
use PHPUnit\Framework\TestCase;
use Slim\Http\Response;

class NotAllowedTest extends TestCase
{
    use MakesHttpRequests;

    /**
     * @test
     */
    public function shouldGetSymfonyResponseWhenCallByNotAllowedMethod(): void
    {
        $container = (new App(new Container()))->getContainer();
        $settings = $container->get('settings');
        $settings['displayErrorDetails'] = true;

        $target = new NotAllowed($container);

        $mockRequest = $this->createServerRequest('POST', '/');

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

        $mockRequest = $this->createServerRequest('POST', '/')
            ->withHeader('ACCEPT', 'application/json');

        $response = $target($mockRequest, new Response(), ['GET']);
        $body = (string)$response->getBody();

        $this->assertSame(405, $response->getStatusCode());
        $this->assertJson($body);
        $this->assertStringContainsString('The POST method is not supported for this route', $body);
        $this->assertStringContainsString('Supported methods: GET', $body);
    }
}

<?php

namespace Tests\Unit;

use Illuminate\Config\Repository;
use LaravelBridge\Slim\ContainerBuilder;
use LaravelBridge\Slim\Handlers\Error as LaravelError;
use LaravelBridge\Slim\Handlers\NotFound as LaravelNotFound;
use PHPUnit\Framework\TestCase;
use Slim\Collection;
use Slim\Handlers\PhpError;
use Slim\Handlers\Strategies\RequestResponse;

class ContainerBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetLaravelInstanceWhenSetTheHandler(): void
    {
        $target = (new ContainerBuilder())
            ->useLaravelErrorHandler()
            ->useLaravelNotFoundHandler()
            ->build()
            ->bootstrap();

        $this->assertInstanceOf(LaravelError::class, $target->get('errorHandler'));
        $this->assertInstanceOf(LaravelNotFound::class, $target->get('notFoundHandler'));

        $this->assertInstanceOf(RequestResponse::class, $target->get('foundHandler'));
        $this->assertInstanceOf(PhpError::class, $target->get('phpErrorHandler'));
    }

    /**
     * @test
     */
    public function shouldReplaceDefaultSettings(): void
    {
        $expected = [
            'httpVersion' => '1.1',
            'responseChunkSize' => 4096,
            'outputBuffering' => 'append',
            'determineRouteBeforeAppMiddleware' => false,
            'displayErrorDetails' => true,
            'addContentLengthHeader' => true,
            'routerCacheFile' => false,
        ];

        $target = (new ContainerBuilder())
            ->setSettings(['displayErrorDetails' => true])
            ->build()
            ->bootstrap();

        $this->assertInstanceOf(Collection::class, $target->get('settings'));
        $this->assertSame($expected, $target->get('settings')->all());
    }

    /**
     * @test
     */
    public function shouldReplaceDefaultSettingsUsingLaravelConfigRepository(): void
    {
        $expected = [
            'httpVersion' => '1.1',
            'responseChunkSize' => 4096,
            'outputBuffering' => 'append',
            'determineRouteBeforeAppMiddleware' => false,
            'displayErrorDetails' => true,
            'addContentLengthHeader' => true,
            'routerCacheFile' => false,
        ];

        $target = (new ContainerBuilder())
            ->setSettings(['displayErrorDetails' => true])
            ->useLaravelSettings()
            ->build()
            ->bootstrap();

        $this->assertInstanceOf(Repository::class, $target->get('settings'));
        $this->assertSame($expected, $target->get('settings')->all());
    }
}

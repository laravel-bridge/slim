<?php

namespace Tests\Unit;

use BadMethodCallException;
use Illuminate\Config\Repository;
use InvalidArgumentException;
use LaravelBridge\Slim\ContainerBuilder;
use LaravelBridge\Slim\Handlers\Error as LaravelError;
use LaravelBridge\Slim\Handlers\NotFound as LaravelNotFound;
use PHPUnit\Framework\TestCase;
use Pimple\Container as PimpleContainer;
use Slim\Collection;
use Slim\Container as SlimContainer;
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

    /**
     * @test
     */
    public function shouldBeOkayWhenUsePimpleContainer(): void
    {
        $pimple = new PimpleContainer();
        $pimple['foo'] = 'foo';
        $pimple['bar'] = function (PimpleContainer $c) {
            return 'bar' . $c['foo'];
        };

        $target = (new ContainerBuilder($pimple))
            ->build();

        $this->assertSame('foo', $target->get('foo'));
        $this->assertSame('barfoo', $target->get('bar'));
    }

    /**
     * @test
     */
    public function shouldBeOkayWhenUseSlimContainer(): void
    {
        $pimple = new SlimContainer();
        $pimple['foo'] = 'foo';
        $pimple['bar'] = function (SlimContainer $c) {
            return 'bar' . $c->get('foo');
        };

        $target = (new ContainerBuilder($pimple))
            ->build();

        $this->assertSame('foo', $target->get('foo'));
        $this->assertSame('barfoo', $target->get('bar'));
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenParamsInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ContainerBuilder('invalid');
    }

    /**
     * @test
     */
    public function shouldMixinContainer(): void
    {
        $target = new ContainerBuilder();
        $target->instance('foo', 'bar');

        $this->assertSame('bar', $target->get('foo'));
    }

    /**
     * @test
     */
    public function shouldMixinContainerAndCanFluentCall(): void
    {
        $target = (new ContainerBuilder())
            ->setupConfig('foo', 'bar')
            ->useLaravelSettings()
            ->buildAndBootstrap();

        $this->assertInstanceOf(Repository::class, $target->get('settings'));
        $this->assertSame('bar', $target->get('config')['foo']);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenMethodNotFound(): void
    {
        $this->expectException(BadMethodCallException::class);

        (new ContainerBuilder())->notFound();
    }
}

<?php

namespace Tests\Unit;

use Illuminate\Support\Fluent;
use LaravelBridge\Slim\Container;
use PHPUnit\Framework\TestCase;
use Slim\Collection;

class ContainerTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetDefaultSettings()
    {
        $target = new Container();

        $expected = [
            'httpVersion' => '1.1',
            'responseChunkSize' => 4096,
            'outputBuffering' => 'append',
            'determineRouteBeforeAppMiddleware' => false,
            'displayErrorDetails' => false,
            'addContentLengthHeader' => true,
            'routerCacheFile' => false,
        ];

        $this->assertInstanceOf(Collection::class, $target->get('settings'));
        $this->assertSame($expected, $target->get('settings')->all());
    }

    /**
     * @test
     */
    public function shouldMergeToDefaultSettings()
    {
        $target = new Container([
            'settings' => [
                'displayErrorDetails' => true,
            ]
        ]);

        $expected = [
            'httpVersion' => '1.1',
            'responseChunkSize' => 4096,
            'outputBuffering' => 'append',
            'determineRouteBeforeAppMiddleware' => false,
            'displayErrorDetails' => true,
            'addContentLengthHeader' => true,
            'routerCacheFile' => false,
        ];

        $this->assertSame($expected, $target->get('settings')->all());
    }

    /**
     * @test
     */
    public function shouldGetDefaultSettingsWithLaravelService()
    {
        $target = new Container([], true);

        $expected = [
            'httpVersion' => '1.1',
            'responseChunkSize' => 4096,
            'outputBuffering' => 'append',
            'determineRouteBeforeAppMiddleware' => false,
            'displayErrorDetails' => false,
            'addContentLengthHeader' => true,
            'routerCacheFile' => false,
        ];

        $this->assertInstanceOf(Fluent::class, $target->get('settings'));
        $this->assertSame($expected, $target->get('settings')->toArray());
    }

    /**
     * @test
     */
    public function shouldMergeToDefaultSettingsWithLaravelService()
    {
        $target = new Container([
            'settings' => [
                'displayErrorDetails' => true,
            ]
        ], true);

        $expected = [
            'httpVersion' => '1.1',
            'responseChunkSize' => 4096,
            'outputBuffering' => 'append',
            'determineRouteBeforeAppMiddleware' => false,
            'displayErrorDetails' => true,
            'addContentLengthHeader' => true,
            'routerCacheFile' => false,
        ];

        $this->assertSame($expected, $target->get('settings')->toArray());
    }
}

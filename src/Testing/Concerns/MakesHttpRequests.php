<?php

declare(strict_types=1);

namespace LaravelBridge\Slim\Testing\Concerns;

use Http\Factory\Slim\ResponseFactory;
use Http\Factory\Slim\ServerRequestFactory;
use Http\Factory\Slim\StreamFactory;
use Http\Factory\Slim\UploadedFileFactory;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

trait MakesHttpRequests
{
    /**
     * @var string
     */
    protected $baseUri = 'http://localhost:8080';

    /**
     * Call the given URI and return the Response.
     *
     * @param string $method
     * @param string $uri
     * @param array $parameters
     * @param array $cookies
     * @param array $files
     * @param array $server
     * @param string|null $content
     * @return ResponseInterface
     */
    protected function call(
        $method,
        $uri,
        $parameters = [],
        $cookies = [],
        $files = [],
        $server = [],
        $content = null
    ): ResponseInterface {
        $request = $this->createServerRequest($method, $uri, $parameters, $cookies, $files, $server, $content);

        $this->instance('request', $request);

        return $this->slim->run($request);
    }

    /**
     * Create Request
     *
     * @param $method
     * @param $uri
     * @param array $parameters
     * @param array $cookies
     * @param array $files
     * @param array $server
     * @param null $content
     * @return ServerRequestInterface
     */
    protected function createServerRequest(
        $method,
        $uri,
        $parameters = [],
        $cookies = [],
        $files = [],
        $server = [],
        $content = null
    ): ServerRequestInterface {
        $symfonyRequest = SymfonyRequest::create(
            $this->prepareUrlForRequest($uri),
            $method,
            $parameters,
            $cookies,
            $files,
            $server,
            $content
        );

        $factory = new PsrHttpFactory(
            new ServerRequestFactory(),
            new StreamFactory(),
            new UploadedFileFactory(),
            new ResponseFactory()
        );

        return $factory->createRequest($symfonyRequest);
    }

    protected function prepareUrlForRequest(string $uri): string
    {
        if (Str::startsWith($uri, '/')) {
            $uri = substr($uri, 1);
        }

        if (!Str::startsWith($uri, 'http')) {
            $uri = $this->baseUri . '/' . $uri;
        }

        return trim($uri, '/');
    }

    /**
     * @param string $baseUri
     * @return static
     */
    public function withBaseUri(string $baseUri)
    {
        $this->baseUri = rtrim($baseUri, '/');

        return $this;
    }
}

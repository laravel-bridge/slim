<?php

namespace LaravelBridge\Slim\Testing\Concerns;

use GuzzleHttp\Psr7\ServerRequest;
use LaravelBridge\Support\Traits\ContainerAwareTrait;
use Psr\Http\Message\ResponseInterface;

trait MakesHttpRequests
{
    use ContainerAwareTrait;

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
    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        $request = $this->createServerRequest($method, $uri, $parameters, $cookies, $files, $server, $content);

        $this->instance('request', $request);

        return $this->app->run($request);
    }

    protected function createServerRequest(
        $method,
        $uri,
        $parameters = [],
        $cookies = [],
        $files = [],
        $server = [],
        $content = null
    ) {
        return (new ServerRequest($method, $uri, [], $content, '1.1', $server))
            ->withQueryParams($parameters)
            ->withCookieParams($cookies)
            ->withUploadedFiles($files);
    }
}

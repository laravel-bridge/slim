<?php

declare(strict_types=1);

namespace LaravelBridge\Slim\Handlers;

use Exception;
use Psr\Http\Message\ResponseInterface as Psr7Response;
use Psr\Http\Message\ServerRequestInterface as Psr7Request;
use Slim\Handlers\NotAllowed as SlimNotAllowed;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class NotAllowed extends AbstractHandler
{
    /**
     * @param Psr7Request $request The most recent Request object
     * @param Psr7Response $response The most recent Response object
     * @param string[] $methods Allowed HTTP methods
     * @return Psr7Response
     */
    public function __invoke(Psr7Request $request, Psr7Response $response, array $methods)
    {
        $exception = $this->createMethodNotAllowedHttpException($methods, $request->getMethod());

        try {
            $laravelResponse = $this->render($this->createLaravelRequestFromPsr7($request), $exception);
        } catch (Exception $ex) {
            return $this->renderSlimNotAllowed($request, $response, $methods);
        }

        return (new DiactorosFactory())->createResponse($laravelResponse);
    }

    /**
     * @param Psr7Request $request
     * @param Psr7Response $response
     * @param string[] $methods Allowed HTTP methods
     * @return Psr7Response
     */
    private function renderSlimNotAllowed(Psr7Request $request, Psr7Response $response, array $methods)
    {
        $slimError = new SlimNotAllowed();

        return $slimError($request, $response, $methods);
    }

    /**
     * Create MethodNotAllowedHttpException instance
     *
     * @param array $others
     * @param string $method
     * @return MethodNotAllowedHttpException
     * @see https://github.com/laravel/framework/blob/v5.8.23/src/Illuminate/Routing/RouteCollection.php#L254
     */
    private function createMethodNotAllowedHttpException(array $others, $method)
    {
        return new MethodNotAllowedHttpException(
            $others,
            sprintf(
                'The %s method is not supported for this route. Supported methods: %s.',
                $method,
                implode(', ', $others)
            )
        );
    }
}

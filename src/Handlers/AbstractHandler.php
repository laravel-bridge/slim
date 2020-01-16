<?php

namespace LaravelBridge\Slim\Handlers;

use Exception;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request as LaravelRequest;
use Illuminate\Http\Response as LaravelResponse;
use Illuminate\Support\Arr;
use LaravelBridge\Support\Traits\ContainerAwareTrait;
use Psr\Http\Message\ServerRequestInterface as Psr7Request;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Debug\ExceptionHandler as SymfonyExceptionHandler;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run as Whoops;

class AbstractHandler
{
    use ContainerAwareTrait;

    /**
     * A list of the internal exception types that should not be reported.
     *
     * @var array
     */
    protected $internalDontReport = [
        HttpException::class,
    ];

    /**
     * Create a new exception handler instance.
     *
     * @param Container $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @return bool
     */
    protected function displayErrorDetails()
    {
        $settings = $this->container->get('settings');

        return isset($settings['displayErrorDetails']) ? (bool)$settings['displayErrorDetails'] : false;
    }

    /**
     * Report or log an exception.
     *
     * @param Exception $e
     * @return void|mixed
     * @see https://github.com/laravel/framework/blob/v5.8.23/src/Illuminate/Foundation/Exceptions/Handler.php#L96
     */
    protected function report(Exception $e)
    {
        if ($this->shouldntReport($e)) {
            return;
        }

        if (is_callable($reportCallable = [$e, 'report'])) {
            return $this->container->call($reportCallable);
        }

        try {
            $logger = $this->container->make(LoggerInterface::class);
        } catch (Exception $ex) {
            return;
        }

        $logger->error($e->getMessage(), ['exception' => $e]);
    }

    /**
     * Determine if the exception is in the "do not report" list.
     *
     * @param Exception $e
     * @return bool
     * @see https://github.com/laravel/framework/blob/v5.8.23/src/Illuminate/Foundation/Exceptions/Handler.php#L135
     */
    protected function shouldntReport(Exception $e)
    {
        foreach ($this->internalDontReport as $type) {
            if ($e instanceof $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * Render an exception into a response.
     *
     * @param LaravelRequest $request
     * @param Exception $e
     * @return SymfonyResponse
     * @see https://github.com/laravel/framework/blob/v5.8.23/src/Illuminate/Foundation/Exceptions/Handler.php#L168
     */
    protected function render($request, Exception $e)
    {
        if (method_exists($e, 'render') && $response = $e->render($request)) {
            return $response;
        }

        // Downward compatibility for Laravel 5.2
        return ($request->ajax() || $request->wantsJson())
            ? $this->prepareJsonResponse($e)
            : $this->prepareResponse($e);
    }

    /**
     * Prepare a JSON response for the given exception.
     *
     * @param Exception $e
     * @return JsonResponse
     * @see https://github.com/laravel/framework/blob/v5.8.23/src/Illuminate/Foundation/Exceptions/Handler.php#L429
     */
    private function prepareJsonResponse(Exception $e)
    {
        return new JsonResponse(
            $this->convertExceptionToArray($e),
            $this->isHttpException($e) ? $e->getStatusCode() : 500,
            $this->isHttpException($e) ? $e->getHeaders() : [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Convert the given exception to an array.
     *
     * @param Exception $e
     * @return array
     * @see https://github.com/laravel/framework/blob/v5.8.23/src/Illuminate/Foundation/Exceptions/Handler.php#L445
     */
    private function convertExceptionToArray(Exception $e)
    {
        return $this->displayErrorDetails() ? [
            'message' => $e->getMessage(),
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => collect($e->getTrace())->map(function ($trace) {
                return Arr::except($trace, ['args']);
            })->all(),
        ] : [
            'message' => 'Server Error',
        ];
    }

    /**
     * Prepare a response for the given exception.
     *
     * @param Exception $e
     * @return SymfonyResponse
     * @see https://github.com/laravel/framework/blob/v5.8.23/src/Illuminate/Foundation/Exceptions/Handler.php#L278
     */
    private function prepareResponse(Exception $e)
    {
        return $this->toLaravelResponse(
            SymfonyResponse::create(
                $this->renderExceptionContent($e),
                $this->isHttpException($e) ? $e->getStatusCode() : 500,
                $this->isHttpException($e) ? $e->getHeaders() : []
            ),
            $e
        );
    }

    /**
     * Get the response content for the given exception.
     *
     * @param Exception $e
     * @return string
     * @see https://github.com/laravel/framework/blob/v5.8.23/src/Illuminate/Foundation/Exceptions/Handler.php#L314
     */
    private function renderExceptionContent(Exception $e)
    {
        return class_exists(Whoops::class)
            ? $this->renderExceptionWithWhoops($e)
            : $this->renderExceptionWithSymfony($e);
    }

    /**
     * @param Exception $e
     * @return string
     * @see https://github.com/laravel/framework/blob/v5.8.23/src/Illuminate/Foundation/Exceptions/Handler.php#L331
     */
    private function renderExceptionWithWhoops(Exception $e)
    {
        return tap(new Whoops(), function (Whoops $whoops) {
            $whoops->pushHandler(new PrettyPageHandler());
            $whoops->writeToOutput(false);
            $whoops->allowQuit(false);
        })->handleException($e);
    }

    /**
     * @param Exception $e
     * @return string
     * @see https://github.com/laravel/framework/blob/v5.8.23/src/Illuminate/Foundation/Exceptions/Handler.php#L359
     */
    private function renderExceptionWithSymfony(Exception $e)
    {
        return (new SymfonyExceptionHandler($this->displayErrorDetails()))->getHtml(
            FlattenException::create($e)
        );
    }

    /**
     * @param SymfonyResponse $response
     * @param Exception $e
     * @return LaravelResponse
     * @see https://github.com/laravel/framework/blob/v5.8.23/src/Illuminate/Foundation/Exceptions/Handler.php#L407
     */
    private function toLaravelResponse($response, Exception $e)
    {
        if ($response instanceof SymfonyRedirectResponse) {
            $response = new RedirectResponse(
                $response->getTargetUrl(),
                $response->getStatusCode(),
                $response->headers->all()
            );
        } else {
            $response = new LaravelResponse(
                $response->getContent(),
                $response->getStatusCode(),
                $response->headers->all()
            );
        }

        return $response->withException($e);
    }

    /**
     * @param Psr7Request $request
     * @return LaravelRequest
     */
    protected function createLaravelRequestFromPsr7(Psr7Request $request)
    {
        /** @var SymfonyRequest $symfonyRequest */
        $symfonyRequest = $this->container->make(HttpFoundationFactory::class)->createRequest($request);

        $laravelRequest = LaravelRequest::createFromBase($symfonyRequest);

        // Compatibility for symfony/http-foundation v2.8.
        // NOTICE: It will use headers bag instead header in server params.
        $laravelRequest->headers = new HeaderBag($symfonyRequest->headers->all());

        return $laravelRequest;
    }

    private function isHttpException(Exception $e)
    {
        return $e instanceof HttpExceptionInterface;
    }
}

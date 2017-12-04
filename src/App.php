<?php

namespace Photogabble\Tuppence;

use League\Container\Container;
use League\Container\ContainerInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Event\EmitterTrait;
use League\Route\RouteCollection;
use Photogabble\Tuppence\ErrorHandlers\ExceptionHandler;
use Photogabble\Tuppence\ErrorHandlers\InvalidHandlerException;
use Photogabble\Tuppence\ErrorHandlers\InvalidHandlerResponseException;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\EmitterInterface;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\ServerRequestFactory;

class App
{
    use EmitterTrait;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var RouteCollection
     */
    private $router;

    /**
     * @var null|ExceptionHandler
     */
    private $exceptionHandler;

    public function __construct(EmitterInterface $emitter = null)
    {
        $this->getContainer()->share('emitter', function () use ($emitter) {
            if (is_null($emitter)) {
                $emitter = new SapiEmitter();
            }

            return $emitter;
        });
    }

    /**
     * Set the Container.
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        $this->container->share(self::class, $this);

        $this->getContainer()->share('response', Response::class);

        $this->router = null;
    }

    /**
     * Get the applications Container.
     *
     * @return ContainerInterface
     */
    public function getContainer()
    {
        if (is_null($this->container)) {
            $this->setContainer(new Container());
            $this->container->delegate(
                new \League\Container\ReflectionContainer()
            );
        }

        return $this->container;
    }

    /**
     * Get the route collection.
     *
     * @return RouteCollection
     */
    public function getRouter()
    {
        if (!isset($this->router)) {
            $this->router = new RouteCollection($this->getContainer());
        }

        return $this->router;
    }

    /**
     * Register a new service provider with the container.
     *
     * @param AbstractServiceProvider $serviceProvider
     */
    public function register(AbstractServiceProvider $serviceProvider)
    {
        $this->getContainer()->addServiceProvider($serviceProvider);
    }

    /**
     * Add a GET route.
     *
     * @param $route
     * @param $action
     */
    public function get($route, $action)
    {
        $this->getRouter()->map('GET', $route, $action);
    }

    /**
     * Add a POST route.
     *
     * @param $route
     * @param $action
     */
    public function post($route, $action)
    {
        $this->getRouter()->map('POST', $route, $action);
    }

    /**
     * Add a PUT route.
     *
     * @param $route
     * @param $action
     */
    public function put($route, $action)
    {
        $this->getRouter()->map('PUT', $route, $action);
    }

    /**
     * Add a DELETE route.
     *
     * @param $route
     * @param $action
     */
    public function delete($route, $action)
    {
        $this->getRouter()->map('DELETE', $route, $action);
    }

    /**
     * Add a PATCH route.
     *
     * @param $route
     * @param $action
     */
    public function patch($route, $action)
    {
        $this->getRouter()->map('PATCH', $route, $action);
    }

    /**
     * Add a OPTIONS route.
     *
     * @param $route
     * @param $action
     */
    public function options($route, $action)
    {
        $this->getRouter()->map('OPTIONS', $route, $action);
    }

    /**
     * @param ServerRequest|null $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Exception
     */
    public function dispatch(ServerRequest $request = null, $catchesExceptions = true)
    {
        $this->getContainer()->share('request', function () use ($request) {
            if (is_null($request)) {
                $request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
            }

            return $request;
        });

        $this->emit('before.dispatch', $this->getContainer()->get('request'));

        try {
            return $this->getRouter()->dispatch(
                $this->getContainer()->get('request'),
                $this->getContainer()->get('response')
            );
        } catch (\Exception $e) {
            if (!$catchesExceptions || is_null($this->exceptionHandler)) {
                throw $e;
            }

            $handler = $this->exceptionHandler;
            $response = $handler($e);

            if (!$response instanceof \Psr\Http\Message\ResponseInterface) {
                throw new InvalidHandlerResponseException('The exception handler ['.get_class($handler).'] did not return a valid response.');
            }

            return $response;
        }
    }

    /**
     * @param ServerRequest|null $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function run(ServerRequest $request = null)
    {
        $response = $this->dispatch($request);
        $this->emit('after.dispatch', $this->getContainer()->get('request'), $response);
        $this->container->get('emitter')->emit($response);

        return $response;
    }

    /**
     * @param null|ExceptionHandler $exceptionHandler
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws InvalidHandlerException
     */
    public function setExceptionHandler($exceptionHandler)
    {
        if (is_string($exceptionHandler)){
            $exceptionHandler = $this->getContainer()->get($exceptionHandler);
        }

        if (!$exceptionHandler instanceof ExceptionHandler) {
            throw new InvalidHandlerException('Exception handlers must implement the ExceptionHandler interface.');
        }

        $this->exceptionHandler = $exceptionHandler;
    }
}

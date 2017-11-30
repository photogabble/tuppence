<?php

namespace Photogabble\Tuppence;

use League\Container\Container;
use League\Container\ContainerInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Event\EmitterTrait;
use League\Route\RouteCollection;
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
     * @param ServerRequest|null $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function dispatch(ServerRequest $request = null)
    {
        $this->getContainer()->share('request', function () use ($request) {
            if (is_null($request)) {
                $request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
            }

            return $request;
        });
        $this->emit('before.dispatch', $this->getContainer()->get('request'));

        return $this->getRouter()->dispatch($this->getContainer()->get('request'), $this->getContainer()->get('response'));
    }

    /**
     * @param ServerRequest|null $request
     * @return \Psr\Http\Message\ResponseInterface
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
}

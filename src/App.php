<?php

namespace Photogabble\Tuppence;

use League\Container\Container;
use League\Route\Middleware\MiddlewareAwareInterface;
use League\Route\Route;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use Psr\Container\ContainerInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Event\EmitterTrait;
use Photogabble\Tuppence\ErrorHandlers\ExceptionHandler;
use Photogabble\Tuppence\ErrorHandlers\InvalidHandlerException;
use Photogabble\Tuppence\ErrorHandlers\InvalidHandlerResponseException;
use Psr\Http\Server\MiddlewareInterface;
use Zend\Diactoros\Response;
use Zend\HttpHandlerRunner\Emitter\EmitterInterface;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\ServerRequestFactory;

class App
{
    use EmitterTrait;

    /**
     * Tuppence Version.
     */
    const VERSION = '2.0.0';

    /**
     * @var Container|ContainerInterface
     */
    private $container;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var null|ExceptionHandler
     */
    private $exceptionHandler;

    /**
     * App constructor.
     * @param EmitterInterface|null $emitter
     */
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
    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
        $this->container->share(self::class, $this);
        $this->container->share('response', Response::class);
        $this->router = null;
    }

    /**
     * Get the applications Container.
     *
     * @return Container
     */
    public function getContainer(): Container
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
     * Get the Router
     *
     * @see http://route.thephpleague.com/4.x/
     * @return Router
     */
    public function getRouter(): Router
    {
        if (!isset($this->router)) {
            $strategy = new ApplicationStrategy();
            $strategy->setContainer($this->getContainer());
            $this->router = (new Router)->setStrategy($strategy);
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
     * @see http://route.thephpleague.com/4.x/routes/
     * @param $route
     * @param $action
     * @return Route
     */
    public function get($route, $action)
    {
        return $this->getRouter()->map('GET', $route, $action);
    }

    /**
     * Add a POST route.
     *
     * @see http://route.thephpleague.com/4.x/routes/
     * @param $route
     * @param $action
     * @return Route
     */
    public function post($route, $action)
    {
        return $this->getRouter()->map('POST', $route, $action);
    }

    /**
     * Add a PUT route.
     *
     * @see http://route.thephpleague.com/4.x/routes/
     * @param $route
     * @param $action
     * @return Route
     */
    public function put($route, $action)
    {
        return $this->getRouter()->map('PUT', $route, $action);
    }

    /**
     * Add a DELETE route.
     *
     * @see http://route.thephpleague.com/4.x/routes/
     * @param $route
     * @param $action
     * @return Route
     */
    public function delete($route, $action)
    {
        return $this->getRouter()->map('DELETE', $route, $action);
    }

    /**
     * Add a PATCH route.
     *
     * @see http://route.thephpleague.com/4.x/routes/
     * @param $route
     * @param $action
     * @return Route
     */
    public function patch($route, $action)
    {
        return $this->getRouter()->map('PATCH', $route, $action);
    }

    /**
     * Add a OPTIONS route.
     *
     * @see http://route.thephpleague.com/4.x/routes/
     * @param $route
     * @param $action
     * @return Route
     */
    public function options($route, $action): Route
    {
        return $this->getRouter()->map('OPTIONS', $route, $action);
    }

    /**
     * Define PSR-15 middleware that executes for the whole application.
     *
     * @see http://route.thephpleague.com/4.x/middleware/
     * @see https://www.php-fig.org/psr/psr-15/
     * @param MiddlewareInterface $middleware
     * @return MiddlewareAwareInterface
     */
    public function middleware(MiddlewareInterface $middleware): MiddlewareAwareInterface
    {
        return $this->getRouter()->middleware($middleware);
    }

    /**
     * @param ServerRequest|null $request
     * @param bool $catchesExceptions
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
                $this->getContainer()->get('request')
            );
        } catch (\Exception $e) {
            if (!$catchesExceptions || is_null($this->exceptionHandler)) {
                throw $e;
            }

            $handler = $this->exceptionHandler;
            $response = $handler($e, $this->getContainer()->get('request'));

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
     * @param string|ExceptionHandler $exceptionHandler
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

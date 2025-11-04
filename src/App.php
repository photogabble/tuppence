<?php

namespace Photogabble\Tuppence;

use Exception;
use League\Container\Container;
use League\Container\DefinitionContainerInterface;
use League\Container\ReflectionContainer;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Route\Route;
use League\Route\Strategy\ApplicationStrategy;
use Photogabble\Tuppence\ErrorHandlers\ExceptionHandler;
use Photogabble\Tuppence\ErrorHandlers\InvalidHandlerException;
use Laminas\Diactoros\Response;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\ServerRequestFactory;
use League\Event\EventDispatcherAware;
use League\Event\EventDispatcherAwareBehavior;
use League\Route\Router;
use Photogabble\Tuppence\Events\AfterDispatch;
use Photogabble\Tuppence\Events\BeforeDispatch;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

class App implements EventDispatcherAware
{
    /**
     * Tuppence Version.
     */
    const VERSION = '2.0.3';

    use EventDispatcherAwareBehavior;

    protected static App $instance;

    private ?DefinitionContainerInterface $container;

    private ?Router $router;

    private ?ExceptionHandler $exceptionHandler;

    public function __construct(
        EmitterInterface              $emitter = null,
        ?DefinitionContainerInterface $container = null,
        ?Router                       $router = null,
        ?ExceptionHandler             $exceptionHandler = null
    )
    {
        if (is_null($container)) {
            $this->setContainer(new Container());
            $this->container->delegate(new ReflectionContainer());
        } else {
            $this->container = $container;
        }

        if (is_null($router)) {
            /** @var ApplicationStrategy $strategy */
            $strategy = (new ApplicationStrategy)->setContainer($this->container);
            /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
            $this->router = (new Router())->setStrategy($strategy);
        } else {
            $this->router = $router;
        }

        $this->exceptionHandler = $exceptionHandler;

        $this->container->addShared('emitter', function () use ($emitter) {
            if (is_null($emitter)) {
                $emitter = new SapiEmitter();
            }

            return $emitter;
        });

        static::$instance = $this;
    }

    public static function getInstance(): static
    {
        return static::$instance ??= new static;
    }

    public static function setInstance(App $container): void
    {
        static::$instance = $container;
    }

    /**
     * Set the Container.
     *
     * @param DefinitionContainerInterface $container
     */
    public function setContainer(DefinitionContainerInterface $container): void
    {
        $this->container = $container;
        $this->container->addShared(self::class, $this);
        $this->container->addShared('response', Response::class);
        $this->router = null;
    }

    /**
     * Get the applications Container.
     *
     * @return DefinitionContainerInterface
     */
    public function getContainer(): DefinitionContainerInterface
    {
        return $this->container;
    }

    /**
     * Get the route collection.
     *
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * Register a new service provider with the container.
     *
     * @param AbstractServiceProvider $serviceProvider
     */
    public function register(AbstractServiceProvider $serviceProvider): void
    {
        $this->getContainer()->addServiceProvider($serviceProvider);
    }

    /**
     * Add a GET route.
     *
     * @param $route
     * @param $action
     * @return Route
     */
    public function get($route, $action): Route
    {
        return $this->getRouter()->map('GET', $route, $action);
    }

    /**
     * Add a POST route.
     *
     * @param $route
     * @param $action
     * @return Route
     */
    public function post($route, $action): Route
    {
        return $this->getRouter()->map('POST', $route, $action);
    }

    /**
     * Add a PUT route.
     *
     * @param $route
     * @param $action
     * @return Route
     */
    public function put($route, $action): Route
    {
        return $this->getRouter()->map('PUT', $route, $action);
    }

    /**
     * Add a DELETE route.
     *
     * @param $route
     * @param $action
     * @return Route
     */
    public function delete($route, $action): Route
    {
        return $this->getRouter()->map('DELETE', $route, $action);
    }

    /**
     * Add a PATCH route.
     *
     * @param $route
     * @param $action
     * @return Route
     */
    public function patch($route, $action): Route
    {
        return $this->getRouter()->map('PATCH', $route, $action);
    }

    /**
     * Add a OPTIONS route.
     *
     * @param $route
     * @param $action
     * @return Route
     */
    public function options($route, $action): Route
    {
        return $this->getRouter()->map('OPTIONS', $route, $action);
    }

    /**
     * @param ServerRequest|null $request
     * @param bool $catchesExceptions
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function dispatch(?ServerRequest $request = null, bool $catchesExceptions = true): ResponseInterface
    {
        $this->getContainer()->addShared('request', function () use ($request) {
            if (is_null($request)) {
                $request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
            }

            return $request;
        });

        $this->eventDispatcher()->dispatch(new BeforeDispatch($this->getContainer()->get('request')));

        try {
            return $this->getRouter()->dispatch($this->getContainer()->get('request'));
        } catch (Exception $e) {
            if (!$catchesExceptions || is_null($this->exceptionHandler)) throw $e;

            $handler = $this->exceptionHandler;
            return $handler($e, $this->getContainer()->get('request'));
        }
    }

    /**
     * @param ServerRequest|null $request
     * @return ResponseInterface
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function run(ServerRequest $request = null): ResponseInterface
    {
        $response = $this->dispatch($request);

        $this->eventDispatcher()->dispatch(new AfterDispatch($this->getContainer()->get('request'), $response));
        $this->container->get('emitter')->emit($response);

        return $response;
    }

    /**
     * @param string|ExceptionHandler $exceptionHandler
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidHandlerException
     */
    public function setExceptionHandler(string|ExceptionHandler $exceptionHandler): void
    {
        if (is_string($exceptionHandler)) {
            $exceptionHandler = $this->getContainer()->get($exceptionHandler);
        }

        if (!$exceptionHandler instanceof ExceptionHandler) {
            throw new InvalidHandlerException('Exception handlers must implement the ExceptionHandler interface.');
        }

        $this->exceptionHandler = $exceptionHandler;
    }
}

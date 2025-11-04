<?php

namespace Photogabble\Tuppence\Tests\Unit;

use Exception;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use League\Container\Container;
use League\Container\Exception\NotFoundException;
use League\Event\EventDispatcher;
use League\Route\Router;
use Photogabble\Tuppence\App;
use Photogabble\Tuppence\ErrorHandlers\DefaultExceptionHandler;
use Photogabble\Tuppence\ErrorHandlers\InvalidHandlerResponseException;
use Photogabble\Tuppence\Tests\Helpers\TestEmitter;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AppTest extends TestCase
{
    protected $response;
    protected $request;

    public function setUp(): void
    {
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
    }

    public function testInstances()
    {
        $emitter = $this->createMock(EmitterInterface::class);
        $app = new App($emitter);
        $this->assertTrue($app->getContainer() instanceof Container);
        $this->assertTrue($app->getRouter() instanceof Router);
        $this->assertTrue($app->eventDispatcher() instanceof EventDispatcher);
    }

    public function testAppEmitsOnRun()
    {
        $emitter = $this->createMock(EmitterInterface::class);
        $emitter->expects($this->once())->method('emit');

        $app = new App($emitter);
        $app->get('/', function () {
            return new Response;
        });
        $app->run();
    }

    public function testRouteDispatch()
    {
        $emitter = new TestEmitter();
        $app = new App($emitter);

        $invoked = false;

        $app->get('/foo/bar', function () use (&$invoked) {
            $invoked = true;

            return new Response;
        });

        $request = ServerRequestFactory::fromGlobals([
            'HTTP_HOST' => 'example.com',
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/foo/bar',
            'QUERY_STRING' => 'bar=baz',
        ], [], [], [], []);

        /* @var Response $response */
        $app->run($request);
        $this->assertTrue($invoked);
    }

    public function testRouterExceptionThrown()
    {
        $emitter = new TestEmitter();
        $app = new App($emitter);

        $request = ServerRequestFactory::fromGlobals();

        try {
            $app->run($request);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof \League\Route\Http\Exception\NotFoundException);
        }
    }

    public function testCollectionExceptionThrown()
    {
        $emitter = new TestEmitter();
        $app = new App($emitter);
        try {
            $app->getContainer()->get('hello-world');
        } catch (Exception $e) {
            $this->assertTrue($e instanceof NotFoundException);
        }
    }

    public function testExceptionHandlerEmptyString()
    {
        $emitter = new TestEmitter();
        $app = new App($emitter);
        try {
            $app->setExceptionHandler('');
        } catch (Exception $e) {
            $this->assertTrue($e instanceof NotFoundException);
        }
    }

    public function testExceptionHandlerInvalidClass()
    {
        $app = new App(new TestEmitter);

        $this->expectException('TypeError');

        /** @noinspection PhpParamsInspection */
        $app->setExceptionHandler(new TestEmitter());
    }

    public function testExceptionHandlerFailsWhenNotValidResponse()
    {
        $exceptionMock = $this->createMock(DefaultExceptionHandler::class);
        $exceptionMock->expects($this->once())
            ->method('__invoke')
            ->with(
                $this->isInstanceOf(\League\Route\Http\Exception\NotFoundException::class),
                $this->isInstanceOf(RequestInterface::class)
            );

        $emitter = new TestEmitter();
        $app = new App($emitter);

        $app->setExceptionHandler($exceptionMock);

        $request = ServerRequestFactory::fromGlobals();
        try {
            $app->dispatch($request);
        } catch (Exception$e) {
            $this->assertTrue($e instanceof InvalidHandlerResponseException);
        }
    }

    public function testExceptionHandler()
    {
        $exceptionMock = $this->createMock(DefaultExceptionHandler::class);
        $exceptionMock->method('__invoke')->willReturn(new Response());

        $exceptionMock->expects($this->once())
            ->method('__invoke')
            ->with(
                $this->isInstanceOf(\League\Route\Http\Exception\NotFoundException::class),
                $this->isInstanceOf(RequestInterface::class)
            );

        $emitter = new TestEmitter();
        $app = new App($emitter);

        $app->setExceptionHandler($exceptionMock);

        $request = ServerRequestFactory::fromGlobals();
        $app->dispatch($request);
    }

    public function testGetInstance()
    {
        $app = new App(new TestEmitter);
        $app->getContainer()->add('hello-world', 'Hello World!');;

        $instance = App::getInstance();

        $this->assertInstanceOf(App::class, $instance);
        $this->assertSame($app, $instance, 'getInstance should return the same instance');
        $this->assertEquals('Hello World!', $instance->getContainer()->get('hello-world'));
    }

    public function testSetInstance()
    {
        $app = new App(new TestEmitter);
        $app->getContainer()->add('hello-world', 'Hello World!');;

        $app2 = new App(new TestEmitter);

        $instance = App::getInstance();
        $this->assertNotSame($app, $instance);

        $instance = App::getInstance();
        $this->assertNotSame($app, $instance);
        $this->assertSame($app2, $instance);

        App::setInstance($app);

        $instance = App::getInstance();
        $this->assertSame($app, $instance);
        $this->assertNotSame($app2, $instance);
    }

}

<?php

namespace Photogabble\Tuppence\Tests\Feature;

use Laminas\Diactoros\Response;
use Photogabble\Tuppence\ErrorHandlers\DefaultExceptionHandler;
use Psr\Http\Message\ServerRequestInterface;
use Photogabble\Tuppence\Tests\BootsApp;
use Laminas\Diactoros\ServerRequestFactory;

class RoutesTest extends BootsApp
{
    public function testGet()
    {
        $this->app->get('/foo/bar', function () {
            $response = new Response;
            $response->getBody()->write('Hello World!');
            return $response;
        });

        $response = $this->runRequest(ServerRequestFactory::fromGlobals([
            'HTTP_HOST' => 'example.com',
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/foo/bar',
        ], [], [], [], []));

        $this->assertResponseOk();
        $this->assertEquals('Hello World!', $response);
    }

    public function testGetWithQuery()
    {
        $this->app->get('/foo/bar', function (ServerRequestInterface $request) {
            $query = $request->getQueryParams();

            $response = new Response;
            $response->getBody()->write($query['a']);
            return $response;
        });

        $response = $this->runRequest(ServerRequestFactory::fromGlobals([
            'HTTP_HOST' => 'example.com',
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/foo/bar',
            'QUERY_STRING' => '?a=123',
        ], [
            'a' => '123'
        ], [], [], []));

        $this->assertResponseOk();
        $this->assertEquals('123', $response);
    }

    public function testPost()
    {
        $this->app->post('/foo/bar', function () {
            $response = new Response;
            $response->getBody()->write('Hello World!');
            return $response;
        });

        $response = $this->runRequest(ServerRequestFactory::fromGlobals([
            'HTTP_HOST' => 'example.com',
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/foo/bar',
        ], [], [], [], []));

        $this->assertResponseOk();
        $this->assertEquals('Hello World!', $response);
    }

    public function testPut()
    {
        $this->app->put('/foo/bar', function () {
            $response = new Response;
            $response->getBody()->write('Hello World!');
            return $response;
        });

        $response = $this->runRequest(ServerRequestFactory::fromGlobals([
            'HTTP_HOST' => 'example.com',
            'REQUEST_METHOD' => 'PUT',
            'REQUEST_URI' => '/foo/bar',
        ], [], [], [], []));

        $this->assertResponseOk();
        $this->assertEquals('Hello World!', $response);
    }

    public function testPatch()
    {
        $this->app->patch('/foo/bar', function () {
            $response = new Response;
            $response->getBody()->write('Hello World!');
            return $response;
        });

        $response = $this->runRequest(ServerRequestFactory::fromGlobals([
            'HTTP_HOST' => 'example.com',
            'REQUEST_METHOD' => 'PATCH',
            'REQUEST_URI' => '/foo/bar',
        ], [], [], [], []));

        $this->assertResponseOk();
        $this->assertEquals('Hello World!', $response);
    }

    public function testDelete()
    {
        $this->app->delete('/foo/bar', function () {
            $response = new Response;
            $response->getBody()->write('Hello World!');
            return $response;
        });

        $response = $this->runRequest(ServerRequestFactory::fromGlobals([
            'HTTP_HOST' => 'example.com',
            'REQUEST_METHOD' => 'DELETE',
            'REQUEST_URI' => '/foo/bar',
        ], [], [], [], []));

        $this->assertResponseOk();
        $this->assertEquals('Hello World!', $response);
    }

    public function testOptions()
    {
        $this->app->options('/foo/bar', function () {
            $response = new Response;
            $response->getBody()->write('Hello World!');
            return $response;
        });

        $response = $this->runRequest(ServerRequestFactory::fromGlobals([
            'HTTP_HOST' => 'example.com',
            'REQUEST_METHOD' => 'OPTIONS',
            'REQUEST_URI' => '/foo/bar',
        ], [], [], [], []));

        $this->assertResponseOk();
        $this->assertEquals('Hello World!', $response);
    }

    public function testExceptionHandled()
    {
        $this->app->setExceptionHandler(DefaultExceptionHandler::class);
        $response = $this->runRequest(ServerRequestFactory::fromGlobals());

        $this->assertResponseCodeEquals(404);

        $decodedJson = json_decode($response, true);
        $this->assertEquals('Not Found', $decodedJson['message']);
    }
}

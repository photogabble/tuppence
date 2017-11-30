<?php

namespace Photogabble\Tuppence\Tests\Feature;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Photogabble\Tuppence\Tests\BootsApp;
use Zend\Diactoros\ServerRequestFactory;

class RoutesTest extends BootsApp
{
    public function testGet()
    {
        $this->app->get('/foo/bar', function (ServerRequestInterface $request, ResponseInterface $response){
            $response->getBody()->write('Hello World!');
            return $response;
        });

        $response = $this->runRequest(ServerRequestFactory::fromGlobals([
            'HTTP_HOST'      => 'example.com',
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI'    => '/foo/bar',
        ], [], [], [], []));

        $this->assertResponseOk();
        $this->assertEquals('Hello World!', $response);
    }

    public function testGetWithQuery()
    {
        $this->app->get('/foo/bar', function (ServerRequestInterface $request, ResponseInterface $response){
            $query = $request->getQueryParams();

            $response->getBody()->write($query['a']);
            return $response;
        });

        $response = $this->runRequest(ServerRequestFactory::fromGlobals([
            'HTTP_HOST'      => 'example.com',
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI'    => '/foo/bar',
            'QUERY_STRING'   => '?a=123',
        ], [
            'a' => '123'
        ], [], [], []));

        $this->assertResponseOk();
        $this->assertEquals('123', $response);
    }
}

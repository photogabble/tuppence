<?php

namespace Photogabble\Tuppence\Tests\Helpers;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @mixin TestCase
 */
trait MakesRequests
{
    protected function makeRequest(string $method = 'GET', ?array $params = null) {
        $request = $this
            ->getMockBuilder(ServerRequestInterface::class)
            ->getMock();
        $request->method('getMethod')->willReturn($method);
        $request->method('getParsedBody')->willReturn($params);
        return $request;
    }

    protected function makeRequestHandler()
    {
        $delegate = $this
            ->getMockBuilder(RequestHandlerInterface::class)
            ->getMock();
        $delegate->method('handle')->willReturn($this->makeResponse());

        return $delegate;
    }

    protected function makeResponse()
    {
        return $this
            ->getMockBuilder(ResponseInterface::class)
            ->getMock();
    }
}
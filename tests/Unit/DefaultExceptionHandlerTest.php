<?php

namespace Photogabble\Tuppence\Tests\Unit;

use Exception;
use Photogabble\Tuppence\ErrorHandlers\DefaultExceptionHandler;
use Laminas\Diactoros\Response\JsonResponse;
use PHPUnit\Framework\TestCase;
use Laminas\Diactoros\ServerRequestFactory;

class DefaultExceptionHandlerTest extends TestCase
{
    public function testResponse()
    {
        $handler = new DefaultExceptionHandler();
        $exception = new Exception('Test');

        $response = $handler($exception, ServerRequestFactory::fromGlobals());
        $this->assertInstanceOf(JsonResponse::class, $response);

        $jsonDecode = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals('Test', $jsonDecode['message']);
    }

    public function testIgnoreFunctionality()
    {
        $handler = new DefaultExceptionHandler();
        $handler->addIgnored(Exception::class);
        $exception = new Exception('Test');

        try {
            $handler($exception, ServerRequestFactory::fromGlobals());
        } catch (Exception $e) {
            $this->assertSame($exception, $e);
        }
    }
}
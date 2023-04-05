<?php

namespace Photogabble\Tuppence\Tests\Helpers;


use Laminas\Diactoros\ServerRequest;
use Photogabble\Tuppence\App;
use PHPUnit\Framework\TestCase;

class BootsApp extends TestCase
{
    protected App $app;

    protected TestEmitter $emitter;

    public function setUp(): void
    {
        $this->bootApp();
    }

    protected function bootApp()
    {
        $this->emitter = new TestEmitter();
        $this->app = new App($this->emitter);
    }

    protected function runRequest(ServerRequest $request): string
    {
        $this->app->run($request);
        return (string)$this->emitter->getResponse()->getBody();
    }

    protected function assertResponseOk()
    {
        $this->assertEquals(200, $this->emitter->getResponse()->getStatusCode());
    }

    protected function assertResponseCodeEquals($code = 200)
    {
        $this->assertEquals($code, $this->emitter->getResponse()->getStatusCode());
    }
}

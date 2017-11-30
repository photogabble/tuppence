<?php

namespace Tests;


use Photogabble\Tuppence\App;
use Zend\Diactoros\ServerRequest;

class BootsApp extends \PHPUnit_Framework_TestCase
{
    /** @var App */
    protected $app;

    /** @var TestEmitter */
    protected $emitter;

    public function setUp()
    {
        $this->bootApp();
    }

    protected function bootApp()
    {
        $this->emitter = new TestEmitter();
        $this->app = new App($this->emitter);
    }

    protected function runRequest(ServerRequest $request)
    {
        $this->app->run($request);
        return (string)$this->emitter->getResponse()->getBody();
    }

    protected function assertResponseOk()
    {
        $this->assertEquals(200, $this->emitter->getResponse()->getStatusCode());
    }
}

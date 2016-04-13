<?php

namespace Creios\Creiwork\Framework;

use Creios\Creiwork\Framework\Result\TemplateResult;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Uri;
use League\Plates\Engine;

class ResponseBuilderTest extends \PHPUnit_Framework_TestCase
{

//    /** @var ResponseBuilder */
//    private $responseBuilder;
//    /** @var \PHPUnit_Framework_MockObject_MockObject|Engine */
//    private $engine;
//    /** @var \PHPUnit_Framework_MockObject_MockObject|Response */
//    private $response;
//
//    public function setUp()
//    {
//        $this->engine = $this->getMock(Engine::class);
//        $this->responseBuilder = new ResponseBuilder($this->engine);
//        $this->response = new Response();
//    }
//
//    public function testTemplateResult()
//    {
//
//        $this->response->withHeader('Content-Type','text/html')->withBody(new Stream(''));
//        $result = new TemplateResult('test', []);
//        $this->assertEquals(), $this->responseBuilder->process($result));
//    }
}

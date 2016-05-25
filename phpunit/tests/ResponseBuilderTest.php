<?php

namespace Creios\Creiwork\Framework;

use Creios\Creiwork\Framework\Result\TemplateResult;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use League\Plates\Engine;
use Psr\Http\Message\ServerRequestInterface;
use Zumba\Util\JsonSerializer;

/**
 * Class ResponseBuilderTest
 * @package Creios\Creiwork\Framework
 */
class ResponseBuilderTest extends \PHPUnit_Framework_TestCase
{

    /** @var ResponseBuilder */
    private $responseBuilder;
    /** @var \PHPUnit_Framework_MockObject_MockObject|Engine */
    private $engine;
    /** @var \PHPUnit_Framework_MockObject_MockObject|JsonSerializer */
    private $serializer;
    /** @var \PHPUnit_Framework_MockObject_MockObject|ServerRequestInterface */
    private $serverRequest;
    /** @var resource */
    private $stream;

    public function setUp()
    {
        $this->engine = $this->getMock(Engine::class);
        $this->serializer = $this->getMock(JsonSerializer::class);
        $this->serverRequest = $this->getMock(ServerRequestInterface::class);
        $this->responseBuilder = new ResponseBuilder($this->serializer, $this->engine, $this->serverRequest);
        $this->stream = fopen('php://temp', 'r+');
        fwrite($this->stream, '');
        fseek($this->stream, 0);
    }

    public function testTemplateResult()
    {
        $assertedResponse = (new Response())->withHeader('Content-Type', 'text/html')->withBody(new Stream($this->stream));
        $result = new TemplateResult('test', []);
        $actualResponse = $this->responseBuilder->process($result);
        $this->assertEquals($assertedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    public function testTemplateResultWithNullData()
    {

        $assertedResponse = (new Response())->withHeader('Content-Type', 'text/html')->withBody(new Stream($this->stream));
        $result = new TemplateResult('test');
        $actualResponse = $this->responseBuilder->process($result);
        $this->assertEquals($assertedResponse->getHeaders(), $actualResponse->getHeaders());
    }

}

<?php

namespace Creios\Creiwork\Framework;

use Creios\Creiwork\Framework\Result\TemplateResult;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Stream;
use League\Plates\Engine;
use Psr\Http\Message\ServerRequestInterface;
use Zumba\Util\JsonSerializer;

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

    public function setUp()
    {
        $this->engine = $this->getMock(Engine::class);
        $this->serializer = $this->getMock(JsonSerializer::class);
        $this->serverRequest = $this->getMock(ServerRequestInterface::class);
        $this->responseBuilder = new ResponseBuilder($this->serializer, $this->engine, $this->serverRequest);
    }

    public function testTemplateResult()
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, '');
        fseek($stream, 0);
        $assertedResponse = (new Response())->withHeader('Content-Type', 'text/html')->withBody(new Stream($stream));

        $result = new TemplateResult('test', []);
        $actualResponse = $this->responseBuilder->process($result);

        $this->assertEquals($assertedResponse->getHeaders(), $actualResponse->getHeaders());
    }
}

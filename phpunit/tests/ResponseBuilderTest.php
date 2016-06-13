<?php

namespace Creios\Creiwork\Framework;

use Creios\Creiwork\Framework\Result\FileResult;
use Creios\Creiwork\Framework\Result\HtmlResult;
use Creios\Creiwork\Framework\Result\JsonResult;
use Creios\Creiwork\Framework\Result\RedirectResult;
use Creios\Creiwork\Framework\Result\TemplateResult;
use Creios\Creiwork\Framework\Result\Util\Disposition;
use Creios\Creiwork\Framework\Result\XmlResult;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use League\Plates\Engine;
use Psr\Http\Message\ServerRequestInterface;
use Zumba\JsonSerializer\JsonSerializer;

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
        $assertedResponse = (new Response())->withHeader('Content-Type', 'text/html')->withHeader('Content-Length', 0)->withBody(new Stream($this->stream));
        $result = new TemplateResult('test', []);
        $actualResponse = $this->responseBuilder->process($result);
        $this->assertEquals($assertedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    public function testTemplateResultWithStatusCode()
    {
        $assertedResponse = (new Response())->withHeader('Content-Type', 'text/html')->withHeader('Content-Length', 0)->withStatus(200)->withBody(new Stream($this->stream));
        $result = (new TemplateResult('test', []))->withStatusCode(200);
        $actualResponse = $this->responseBuilder->process($result);
        $this->assertEquals($assertedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    public function testTemplateResultWithNullData()
    {

        $assertedResponse = (new Response())->withHeader('Content-Type', 'text/html')->withHeader('Content-Length', 0)->withBody(new Stream($this->stream));
        $result = new TemplateResult('test');
        $actualResponse = $this->responseBuilder->process($result);
        $this->assertEquals($assertedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    public function testJsonResultDownload()
    {
        $assertedResponse = (new Response())->withHeader('Content-Type', 'application/json')->withHeader('Content-Length', 0)
            ->withHeader('Content-Disposition', 'attachment; filename=test.json')
            ->withBody(new Stream($this->stream));
        $disposition = (new Disposition(Disposition::ATTACHMENT))->withFilename('test.json');
        $result = (new JsonResult(['key' => 'value']))->withDisposition($disposition);
        $actualResponse = $this->responseBuilder->process($result);
        $this->assertEquals($assertedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    public function testRedirectResult()
    {
        $assertedResponse = (new Response())->withHeader('Location', 'http://localhost/redirect');
        $result = new RedirectResult('http://localhost/redirect');
        $actualResponse = $this->responseBuilder->process($result);
        $this->assertEquals($assertedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    public function testPlainResult()
    {
        $assertedResponse = (new Response())->withHeader('Content-Type', 'text/plain')->withHeader('Content-Length', 21);
        $result = 'Result is a plaintext';
        $actualResponse = $this->responseBuilder->process($result);
        $this->assertEquals($assertedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    public function testFileResult()
    {
        $assertedResponse = (new Response())->withHeader('Content-Type', 'text/plain')->withHeader('Content-Length', 40);
        $result = new  FileResult(__DIR__ . '/../asset/textfile.txt');
        $actualResponse = $this->responseBuilder->process($result);
        $this->assertEquals($assertedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    public function testHtmlResult()
    {
        $assertedResponse = (new Response())->withHeader('Content-Type', 'text/html')->withHeader('Content-Length', 123);
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>

</body>
</html>
HTML;
        $result = new HtmlResult($html);
        $actualResponse = $this->responseBuilder->process($result);
        $this->assertEquals($assertedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    public function testXmlResult()
    {
        $assertedResponse = (new Response())->withHeader('Content-Type', 'text/xml')->withHeader('Content-Length', 82);
        $xml = <<<XML
<user>
    <id>1</id>
    <firstname>john</firstname>
    <name>doe</name>
</user>
XML;
        $result = new XmlResult($xml);
        $actualResponse = $this->responseBuilder->process($result);
        $this->assertEquals($assertedResponse->getHeaders(), $actualResponse->getHeaders());
    }

}

<?php

namespace Creios\Creiwork\Framework\Router;

use Creios\Creiwork\Framework\Result\ApacheFileResult;
use Creios\Creiwork\Framework\Result\CsvResult;
use Creios\Creiwork\Framework\Result\FileResult;
use Creios\Creiwork\Framework\Result\NginxFileResult;
use Creios\Creiwork\Framework\Result\NoContentResult;
use Creios\Creiwork\Framework\Result\RedirectResult;
use Creios\Creiwork\Framework\Result\SerializableResult;
use Creios\Creiwork\Framework\Result\StreamResult;
use Creios\Creiwork\Framework\Result\StringResult;
use Creios\Creiwork\Framework\Result\TemplateResult;
use Creios\Creiwork\Framework\Result\Util\Disposition;
use Creios\Creiwork\Framework\Router\PostProcessor;
use Creios\Creiwork\Framework\StatusCodes;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use JMS\Serializer\Serializer;
use League\Plates\Engine;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ResponseBuilderTest
 * @package Creios\Creiwork\Framework
 */
class PostProcessorTest extends \PHPUnit_Framework_TestCase
{

    /** @var PostProcessor */
    private $responseBuilder;
    /** @var \PHPUnit_Framework_MockObject_MockObject|Engine */
    private $engine;
    /** @var \PHPUnit_Framework_MockObject_MockObject|Serializer */
    private $serializer;
    /** @var \PHPUnit_Framework_MockObject_MockObject|ServerRequestInterface */
    private $serverRequest;
    /** @var resource */
    private $stream;

    public function setUp()
    {
        $this->engine = $this->createMock(Engine::class);
        $this->serializer = $this->createMock(Serializer::class);
        $this->serverRequest = $this->createMock(ServerRequestInterface::class);
        $this->responseBuilder = new PostProcessor($this->serializer, $this->engine, $this->serverRequest);
        $this->stream = fopen('php://temp', 'r+');
        fwrite($this->stream, '');
        fseek($this->stream, 0);
    }

    public function testTemplateResult()
    {
        $expectedResponse = (new Response())->withHeader('Content-Type', 'text/html')->withHeader('Content-Length', 0)->withBody(new Stream($this->stream));
        $result = new TemplateResult('test', []);
        $actualResponse = $this->responseBuilder->process($this->serverRequest, $result);
        $this->assertEquals($expectedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    public function testTemplateResultWithStatusCode()
    {
        $expectedResponse = (new Response())->withStatus(StatusCodes::HTTP_OK)->withHeader('Content-Type', 'text/html')->withHeader('Content-Length', 0)->withBody(new Stream($this->stream));
        $result = (new TemplateResult('test', []))->withStatusCode(StatusCodes::HTTP_OK);
        $actualResponse = $this->responseBuilder->process($this->serverRequest, $result);
        $this->assertEquals($expectedResponse->getStatusCode(), $actualResponse->getStatusCode());
        $this->assertEquals($expectedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    public function testTemplateResultWithNullData()
    {

        $expectedResponse = (new Response())->withHeader('Content-Type', 'text/html')->withHeader('Content-Length', 0)->withBody(new Stream($this->stream));
        $result = new TemplateResult('test');
        $actualResponse = $this->responseBuilder->process($this->serverRequest, $result);
        $this->assertEquals($expectedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    public function testSerializableJsonResultDownload()
    {
        $expectedResponse = (new Response())->withHeader('Content-Type', 'application/json')
            // Using 0 because serializer is not fully mocked
            ->withHeader('Content-Length', 0)
            ->withHeader('Content-Disposition', 'attachment; filename=test.json')
            ->withBody(new Stream($this->stream));
        $disposition = (new Disposition(Disposition::ATTACHMENT))->withFilename('test.json');
        $result = SerializableResult::createJsonResult(['key' => 'value'])->withDisposition($disposition);
        $actualResponse = $this->responseBuilder->process($this->serverRequest, $result);
        $this->assertEquals($expectedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    public function testSerializableXmlResultDownload()
    {
        $expectedResponse = (new Response())
            ->withHeader('Content-Type', 'text/xml')
            // Using 0 because serializer is not fully mocked
            ->withHeader('Content-Length', 0)
            ->withHeader('Content-Disposition', 'attachment; filename=test.xml')
            ->withBody(new Stream($this->stream));
        $disposition = (new Disposition(Disposition::ATTACHMENT))->withFilename('test.xml');
        $result = SerializableResult::createXmlResult(['key' => 'value'])->withDisposition($disposition);
        $actualResponse = $this->responseBuilder->process($this->serverRequest, $result);
        $this->assertEquals($expectedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    public function testSerializablePlainTextResultDownload()
    {
        $expectedResponse = (new Response())
            ->withHeader('Content-Type', 'text/plain')
            ->withHeader('Content-Length', 29)
            ->withHeader('Content-Disposition', 'attachment; filename=test.txt')
            ->withBody(new Stream($this->stream));
        $disposition = (new Disposition(Disposition::ATTACHMENT))->withFilename('test.txt');
        $result = SerializableResult::createPlainTextResult(['key' => 'value'])->withDisposition($disposition);
        $actualResponse = $this->responseBuilder->process($this->serverRequest, $result);
        $this->assertEquals($expectedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    public function testSerializableHtmlResultDownload()
    {
        $expectedResponse = (new Response())
            ->withHeader('Content-Type', 'text/html')
            // Using 0 because serializer is not fully mocked
            ->withHeader('Content-Length', 0)
            ->withHeader('Content-Disposition', 'attachment; filename=test.html')
            ->withBody(new Stream($this->stream));
        $disposition = (new Disposition(Disposition::ATTACHMENT))->withFilename('test.html');
        $result = SerializableResult::createHtmlResult(['key' => 'value'])->withDisposition($disposition);
        $actualResponse = $this->responseBuilder->process($this->serverRequest, $result);
        $this->assertEquals($expectedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    public function testRedirectResult()
    {
        $expectedResponse = (new Response())->withStatus(StatusCodes::HTTP_FOUND)->withHeader('Location', 'http://localhost/redirect');
        $result = new RedirectResult('http://localhost/redirect');
        $actualResponse = $this->responseBuilder->process($this->serverRequest, $result);
        $this->assertEquals($expectedResponse->getHeaders(), $actualResponse->getHeaders());
        $this->assertEquals($expectedResponse->getStatusCode(), $actualResponse->getStatusCode());

        $this->serverRequest->method('getServerParams')->willReturn(['REQUEST_URI' => 'http://localhost/redirect2']);
        $expectedResponse = (new Response())->withStatus(StatusCodes::HTTP_FOUND)->withHeader('Location', 'http://localhost/redirect2');
        $result = new RedirectResult();
        $actualResponse = $this->responseBuilder->process($this->serverRequest, $result);
        $this->assertEquals($expectedResponse->getHeaders(), $actualResponse->getHeaders());
        $this->assertEquals($expectedResponse->getStatusCode(), $actualResponse->getStatusCode());
    }

    public function testPlainResult()
    {
        $expectedResponse = (new Response())->withHeader('Content-Type', 'text/plain')->withHeader('Content-Length', 21);
        $result = 'Result is a plaintext';
        $actualResponse = $this->responseBuilder->process($this->serverRequest, $result);
        $this->assertEquals($expectedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    public function testPlainTextResult()
    {
        $expectedResponse = (new Response())->withStatus(StatusCodes::HTTP_NOT_FOUND)->withHeader('Content-Type', 'text/plain')->withHeader('Content-Length', 21);
        $result = StringResult::createPlainTextResult('Result is a plaintext')->withStatusCode(StatusCodes::HTTP_NOT_FOUND);
        $actualResponse = $this->responseBuilder->process($this->serverRequest, $result);
        $this->assertEquals($expectedResponse->getStatusCode(), $actualResponse->getStatusCode());
        $this->assertEquals($expectedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    public function testFileResult()
    {
        $expectedResponse = (new Response())->withHeader('Content-Type', 'text/plain')->withHeader('Content-Length', 40);
        $result = new FileResult(__DIR__ . '/../../asset/textfile.txt');
        $actualResponse = $this->responseBuilder->process($this->serverRequest, $result);
        $this->assertEquals($expectedResponse->getHeaders(), $actualResponse->getHeaders());

        $expectedResponse = (new Response())->withHeader('Content-Type', 'text/plain')->withHeader('Content-Length', 40);
        $result = (new FileResult(__DIR__ . '/../../asset/textfile.txt'))->withMimeType('text/plain');
        $actualResponse = $this->responseBuilder->process($this->serverRequest, $result);
        $this->assertEquals($expectedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    public function testHtmlResult()
    {
        $expectedResponse = (new Response())->withHeader('Content-Type', 'text/html')->withHeader('Content-Length', 123);
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
        $result = StringResult::createHtmlResult($html);
        $actualResponse = $this->responseBuilder->process($this->serverRequest, $result);
        $this->assertEquals($expectedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    public function testXmlResult()
    {
        $expectedResponse = (new Response())->withHeader('Content-Type', 'text/xml')->withHeader('Content-Length', 82);
        $xml = <<<XML
<user>
    <id>1</id>
    <firstname>john</firstname>
    <name>doe</name>
</user>
XML;
        $result = StringResult::createXmlResult($xml);
        $actualResponse = $this->responseBuilder->process($this->serverRequest, $result);
        $this->assertEquals($expectedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    public function testCsvResult()
    {
        $csvResult = (new CsvResult([
            ['A', 'B', 'C'],
            [1, 2, 3],
        ]))->withDisposition(
            (new Disposition(Disposition::ATTACHMENT))
                ->withFilename('foobar.csv'));
        $response = $this->responseBuilder->process($this->serverRequest, $csvResult);
        $this->assertEquals(['text/csv'], $response->getHeader('Content-Type'));
        $this->assertEquals(
            ['attachment; filename=foobar.csv'],
            $response->getHeader('Content-Disposition'));
        $csv = <<<CSV
A,B,C
1,2,3

CSV;
        $this->assertEquals($csv, $response->getBody()->getContents());
    }

    public function testApacheFileResult()
    {
        $expectedResponse = (new Response())
            ->withHeader('Content-Type', 'application/octet-stream')
            ->withHeader('X-Sendfile', __DIR__ . '/../asset/textfile.txt')
            ->withHeader('Content-Disposition', 'attachment; filename="textfile.txt"');
        $result = new ApacheFileResult(__DIR__ . '/../asset/textfile.txt');
        $actualResponse = $this->responseBuilder->process($this->serverRequest, $result);
        $this->assertEquals($expectedResponse->getHeaders(), $actualResponse->getHeaders());

        $expectedResponse = (new Response())
            ->withHeader('Content-Type', 'text/plain')
            ->withHeader('X-Sendfile', __DIR__ . '/../asset/textfile.txt')
            ->withHeader('Content-Disposition', 'attachment; filename="textfile.txt"');
        $result = (new ApacheFileResult(__DIR__ . '/../asset/textfile.txt'))->withMimeType('text/plain');
        $actualResponse = $this->responseBuilder->process($this->serverRequest, $result);
        $this->assertEquals($expectedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    public function testNginxFileResult()
    {
        $expectedResponse = (new Response())
            ->withHeader('Content-Type', 'application/octet-stream')
            ->withHeader('X-Accel-Redirect', __DIR__ . '/../asset/textfile.txt')
            ->withHeader('Content-Disposition', 'attachment; filename="textfile.txt"');
        $result = new NginxFileResult(__DIR__ . '/../asset/textfile.txt');
        $actualResponse = $this->responseBuilder->process($this->serverRequest, $result);
        $this->assertEquals($expectedResponse->getHeaders(), $actualResponse->getHeaders());

        $expectedResponse = (new Response())
            ->withHeader('Content-Type', 'text/plain')
            ->withHeader('X-Accel-Redirect', __DIR__ . '/../asset/textfile.txt')
            ->withHeader('Content-Disposition', 'attachment; filename="textfile.txt"');
        $result = (new NginxFileResult(__DIR__ . '/../asset/textfile.txt'))->withMimeType('text/plain');
        $actualResponse = $this->responseBuilder->process($this->serverRequest, $result);
        $this->assertEquals($expectedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    public function testStreamResult()
    {
        $expectedResponse = (new Response())
            ->withHeader('Content-Type', 'text/plain')
            ->withHeader('Content-Length', 11);
        $testResource = fopen('php://memory', 'rw');
        fwrite($testResource, 'Test string');
        fseek($testResource, 0);
        $result = (new StreamResult($testResource, 'text/plain'));
        $response = $this->responseBuilder->process($this->serverRequest, $result);
        $this->assertEquals($expectedResponse->getHeaders(), $response->getHeaders());
        $this->assertEquals('Test string', $response->getBody()->getContents());
    }

    public function testNoContentResult()
    {
        $expectedResponse = (new Response())->withStatus(StatusCodes::HTTP_NO_CONTENT);
        $result = new NoContentResult();
        $response = $this->responseBuilder->process($this->serverRequest, $result);
        $this->assertEquals($expectedResponse->getStatusCode(), $response->getStatusCode());
    }

}

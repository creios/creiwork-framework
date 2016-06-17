<?php

namespace Creios\Creiwork\Framework;

use Creios\Creiwork\Framework\Result\FileResult;
use Creios\Creiwork\Framework\Result\HtmlRawResult;
use Creios\Creiwork\Framework\Result\Interfaces\DisposableResultInterface;
use Creios\Creiwork\Framework\Result\Interfaces\StatusCodeResultInterface;
use Creios\Creiwork\Framework\Result\JsonRawResult;
use Creios\Creiwork\Framework\Result\JsonResult;
use Creios\Creiwork\Framework\Result\RedirectResult;
use Creios\Creiwork\Framework\Result\StreamResult;
use Creios\Creiwork\Framework\Result\StringBufferResult;
use Creios\Creiwork\Framework\Result\TemplateResult;
use Creios\Creiwork\Framework\Result\Traits\StatusCodeResult;
use Creios\Creiwork\Framework\Result\Util\Result;
use Creios\Creiwork\Framework\Result\XmlRawResult;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use League\Plates\Engine;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TimTegeler\Routerunner\PostProcessor\PostProcessorInterface;
use Zumba\JsonSerializer\JsonSerializer;

/**
 * Class ResponseBuilder
 * @package Creios\Creiwork\Util
 */
class ResponseBuilder implements PostProcessorInterface
{

    /**
     * @var Engine
     */
    protected $templateEngine;
    /**
     * @var JsonSerializer
     */
    protected $jsonSerializer;
    /**
     * @var ServerRequestInterface
     */
    protected $serverRequest;

    /**
     * OutputLayer constructor.
     * @param JsonSerializer $jsonSerializer
     * @param Engine $templateEngine
     * @param ServerRequestInterface $serverRequest
     */
    public function __construct(JsonSerializer $jsonSerializer, Engine $templateEngine, ServerRequestInterface $serverRequest)
    {
        $this->jsonSerializer = $jsonSerializer;
        $this->templateEngine = $templateEngine;
        $this->serverRequest = $serverRequest;
    }

    /**
     * @param Result|string $output
     * @return Response
     */
    public function process($output)
    {
        $response = (new Response())->withProtocolVersion('1.1');

        if ($output instanceof DisposableResultInterface) {
            $response = $this->modifyResponseForDisposableResult($response, $output);
        }

        if ($output instanceof TemplateResult) {
            $response = $this->modifyResponseForTemplateResult($response, $output);
        } else if ($output instanceof JsonResult) {
            $response = $this->modifyResponseForJsonResult($response, $output);
        } else if ($output instanceof JsonRawResult) {
            $response = $this->modifyResponseForJsonRawResult($response, $output);
        } else if ($output instanceof RedirectResult) {
            $response = $this->modifyResponseForRedirectResult($response, $output);
        } elseif ($output instanceof FileResult) {
            $response = $this->modifyResponseForFileResult($response, $output);
        } elseif ($output instanceof StringBufferResult) {
            $response = $this->modifyResponseForStringBufferResult($response, $output);
        } elseif ($output instanceof HtmlRawResult) {
            $response = $this->modifyResponseForHtmlRawResult($response, $output);
        } elseif ($output instanceof XmlRawResult) {
            $response = $this->modifyResponseForXmlRawResult($response, $output);
        } elseif ($output instanceof StreamResult) {
            $response = $this->modifyResponseForStreamResult($response, $output);
        } else {
            $response = $this->modifyResponseForPlain($response, $output);
        }

        if ($output instanceof StatusCodeResult) {
            $response = $this->modifyResponseForStatusCodeResult($response, $output);
        }

        return $response;
    }

    /**
     * @param ResponseInterface $response
     * @param DisposableResultInterface $disposable
     * @return ResponseInterface
     */
    private function modifyResponseForDisposableResult(ResponseInterface $response, DisposableResultInterface $disposable)
    {
        $disposition = $disposable->getDisposition();
        if ($disposition !== null) {
            $header = $disposition->getType();
            if ($disposition->getFilename() !== null) {
                $header .= "; filename={$disposition->getFilename()}";
            }
            return $response->withHeader('Content-Disposition', $header);
        }
        return $response;
    }

    /**
     * @param ResponseInterface $response
     * @param TemplateResult $templateResult
     * @return ResponseInterface
     */
    private function modifyResponseForTemplateResult(ResponseInterface $response, TemplateResult $templateResult)
    {
        $this->templateEngine->addData(['host' => 'http://' . $this->serverRequest->getServerParams()['HTTP_HOST'] . '/']);
        if ($templateResult->getData() === null) {
            $data = [];
        } else {
            $data = $templateResult->getData();
        }
        $stream = \GuzzleHttp\Psr7\stream_for($this->templateEngine->render($templateResult->getTemplate(), $data));
        $response = $this->modifyResponseWithContentLength($response, $stream);
        return $response->withHeader('Content-Type', 'text/html')->withBody($stream);
    }

    /**
     * @param ResponseInterface $response
     * @param JsonResult $jsonResult
     * @return static
     */
    private function modifyResponseForJsonResult(ResponseInterface $response, JsonResult $jsonResult)
    {
        $json = $this->jsonSerializer->serialize($jsonResult->getData());
        $stream = \GuzzleHttp\Psr7\stream_for($json);
        $response = $this->modifyResponseWithContentLength($response, $stream);
        return $response->withHeader('Content-Type', 'application/json')->withBody($stream);
    }

    /**
     * @param ResponseInterface $response
     * @param JsonRawResult $jsonRawResult
     * @return ResponseInterface
     */
    private function modifyResponseForJsonRawResult(ResponseInterface $response, JsonRawResult $jsonRawResult)
    {
        $stream = \GuzzleHttp\Psr7\stream_for($jsonRawResult->getJson());
        $response = $this->modifyResponseWithContentLength($response, $stream);
        return $response->withHeader('Content-Type', 'application/json')->withBody($stream);
    }

    /**
     * @param ResponseInterface $response
     * @param RedirectResult $redirectResult
     * @return ResponseInterface
     */
    private function modifyResponseForRedirectResult(ResponseInterface $response, RedirectResult $redirectResult)
    {
        if ($redirectResult->getUrl() == null) {
            return $response->withHeader('Location', $this->serverRequest->getServerParams()['REQUEST_URI']);
        } else {
            return $response->withHeader('Location', $redirectResult->getUrl());
        }
    }

    /**
     * @param ResponseInterface $response
     * @param FileResult $fileResult
     * @return ResponseInterface
     */
    private function modifyResponseForFileResult(ResponseInterface $response, FileResult $fileResult)
    {
        if ($fileResult->getMimeType() != null) {
            $mimeType = $fileResult->getMimeType();
        } else {
            $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->file($fileResult->getPath());
        }
        $stream = \GuzzleHttp\Psr7\stream_for(fopen($fileResult->getPath(), 'r'));
        $response = $this->modifyResponseWithContentLength($response, $stream);
        return $response->withHeader('Content-Type', $mimeType)->withBody($stream);
    }

    /**
     * @param ResponseInterface $response
     * @param StringBufferResult $stringBufferResult
     * @return ResponseInterface
     */
    private function modifyResponseForStringBufferResult(ResponseInterface $response, StringBufferResult $stringBufferResult)
    {
        if ($stringBufferResult->getMimeType() != null) {
            $mimeType = $stringBufferResult->getMimeType();
        } else {
            $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->buffer($stringBufferResult->getBuffer());
        }
        $stream = \GuzzleHttp\Psr7\stream_for($stringBufferResult->getBuffer());
        $response = $this->modifyResponseWithContentLength($response, $stream);
        return $response->withHeader('Content-Type', $mimeType)->withBody($stream);
    }

    /**
     * @param ResponseInterface $response
     * @param HtmlRawResult $htmlResult
     * @return ResponseInterface
     */
    private function modifyResponseForHtmlRawResult(ResponseInterface $response, HtmlRawResult $htmlResult)
    {
        $stream = \GuzzleHttp\Psr7\stream_for($htmlResult->getHtml());
        $response = $this->modifyResponseWithContentLength($response, $stream);
        return $response->withHeader('Content-Type', 'text/html')->withBody($stream);
    }

    /**
     * @param ResponseInterface $response
     * @param XmlRawResult $xmlResult
     * @return ResponseInterface
     */
    private function modifyResponseForXmlRawResult(ResponseInterface $response, XmlRawResult $xmlResult)
    {
        $stream = \GuzzleHttp\Psr7\stream_for($xmlResult->getXml());
        $response = $this->modifyResponseWithContentLength($response, $stream);
        return $response->withHeader('Content-Type', 'text/xml')->withBody($stream);
    }

    /**
     * @param ResponseInterface $response
     * @param StreamResult $streamResult
     * @return ResponseInterface
     */
    private function modifyResponseForStreamResult(ResponseInterface $response, StreamResult $streamResult)
    {
        $stream = \GuzzleHttp\Psr7\stream_for($streamResult->getStream());
        $response = $this->modifyResponseWithContentLength($response, $stream);
        return $response->withHeader('Content-Type', $streamResult->getMimeType())->withBody($stream);
    }

    /**
     * @param ResponseInterface $response
     * @param string $output
     * @return ResponseInterface
     */
    private function modifyResponseForPlain(ResponseInterface $response, $output)
    {
        $stream = \GuzzleHttp\Psr7\stream_for($output);
        $response = $this->modifyResponseWithContentLength($response, $stream);
        return $response->withHeader('Content-Type', 'text/plain')->withBody($stream);
    }

    /**
     * @param ResponseInterface $response
     * @param StatusCodeResultInterface $statusCodeResult
     * @return ResponseInterface
     */
    private function modifyResponseForStatusCodeResult(ResponseInterface $response, StatusCodeResultInterface $statusCodeResult)
    {
        if ($statusCodeResult->getStatusCode() != null) {
            $response = $response->withStatus($statusCodeResult->getStatusCode());
        }
        return $response;
    }

    /**
     * @param ResponseInterface $response
     * @param Stream $stream
     * @return ResponseInterface
     */
    private function modifyResponseWithContentLength(ResponseInterface $response, Stream $stream)
    {
        $size = $stream->getSize();
        if ($size !== null) {
            return $response->withHeader('Content-Length', $stream->getSize());
        } else {
            return $response;
        }
    }

}

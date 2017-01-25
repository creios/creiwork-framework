<?php

namespace Creios\Creiwork\Framework;

use Creios\Creiwork\Framework\Result\Abstracts\AbstractFileResult;
use Creios\Creiwork\Framework\Result\ApacheFileResult;
use Creios\Creiwork\Framework\Result\CsvResult;
use Creios\Creiwork\Framework\Result\FileResult;
use Creios\Creiwork\Framework\Result\Interfaces\DisposableResultInterface;
use Creios\Creiwork\Framework\Result\Interfaces\StatusCodeResultInterface;
use Creios\Creiwork\Framework\Result\JsonResult;
use Creios\Creiwork\Framework\Result\NginxFileResult;
use Creios\Creiwork\Framework\Result\RedirectResult;
use Creios\Creiwork\Framework\Result\StreamResult;
use Creios\Creiwork\Framework\Result\StringBufferResult;
use Creios\Creiwork\Framework\Result\StringResult;
use Creios\Creiwork\Framework\Result\TemplateResult;
use Creios\Creiwork\Framework\Result\Util\Result;
use GuzzleHttp\Psr7\Response;
use League\Plates\Engine;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use TimTegeler\Routerunner\PostProcessor\PostProcessorInterface;
use Zumba\Util\JsonSerializer;

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
     * @return ResponseInterface
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
        } else if ($output instanceof RedirectResult) {
            $response = $this->modifyResponseForRedirectResult($response, $output);
        } elseif ($output instanceof FileResult) {
            $response = $this->modifyResponseForFileResult($response, $output);
        } elseif ($output instanceof ApacheFileResult) {
            $response = $this->modifyResponseForWebServerFileResult($response, $output, 'X-Sendfile');
        } elseif ($output instanceof NginxFileResult) {
            $response = $this->modifyResponseForWebServerFileResult($response, $output, 'X-Accel-Redirect');
        } elseif ($output instanceof StringBufferResult) {
            $response = $this->modifyResponseForStringBufferResult($response, $output);
        } elseif ($output instanceof StreamResult) {
            $response = $this->modifyResponseForStreamResult($response, $output);
        } elseif ($output instanceof StringResult) {
            $response = $this->modifyResponseForStringResult($response, $output);
        } elseif ($output instanceof CsvResult) {
            $response = $this->modifyResponseForCsvResult($response, $output);
        } else {
            $response = $this->modifyResponseForStringResult($response, ResultFactory::createPlainTextResult($output));
        }

        if ($output instanceof StatusCodeResultInterface) {
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
     * @param StreamInterface $stream
     * @return ResponseInterface
     */
    private function modifyResponseWithContentLength(ResponseInterface $response, StreamInterface $stream)
    {
        $size = $stream->getSize();
        if ($size !== null) {
            return $response->withHeader('Content-Length', $stream->getSize());
        } else {
            return $response;
        }
    }

    /**
     * @param ResponseInterface $response
     * @param JsonResult $jsonResult
     * @return ResponseInterface
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
     * @param RedirectResult $redirectResult
     * @return ResponseInterface
     */
    private function modifyResponseForRedirectResult(ResponseInterface $response, RedirectResult $redirectResult)
    {
        if ($redirectResult->getUrl() == null) {
            $response = $response->withHeader('Location', $this->serverRequest->getServerParams()['REQUEST_URI']);
        } else {
            $response = $response->withHeader('Location', $redirectResult->getUrl());
        }
        return $response->withStatus(StatusCodes::HTTP_FOUND);
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
     * @param AbstractFileResult $result
     * @param string $redirectHeaderKey
     * @return ResponseInterface
     */
    private function modifyResponseForWebServerFileResult(ResponseInterface $response, AbstractFileResult $result, $redirectHeaderKey)
    {
        if ($result->getMimeType() != null) {
            $mimeType = $result->getMimeType();
        } else {
            $mimeType = 'application/octet-stream';
        }
        if ($result->getDisposition() === null) {
            $response = $response->withHeader('Content-Disposition', sprintf('attachment; filename="%s"', basename($result->getPath())));
        }
        return $response->withHeader($redirectHeaderKey, $result->getPath())->withHeader('Content-Type', $mimeType);
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
     * @param StringResult $stringResult
     * @return ResponseInterface
     */
    private function modifyResponseForStringResult(ResponseInterface $response, StringResult $stringResult)
    {
        $stream = \GuzzleHttp\Psr7\stream_for($stringResult->getPlainText());
        $response = $this->modifyResponseWithContentLength($response, $stream);
        return $response->withHeader('Content-Type', $stringResult->getMimeType())->withBody($stream);
    }

    /**
     * @param ResponseInterface $response
     * @param CsvResult $csvResult
     * @return ResponseInterface
     */
    private function modifyResponseForCsvResult(
        ResponseInterface $response,
        CsvResult $csvResult
    )
    {
        $resource = fopen('php://temp', 'r+');
        foreach ($csvResult->getData() as $row) {
            fputcsv($resource, $row);
        }
        rewind($resource);
        $stream = \GuzzleHttp\Psr7\stream_for($resource);
        return $response
            ->withHeader('Content-Type', 'text/csv')
            ->withBody($stream);
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
        } else {
            $response = $response->withStatus(StatusCodes::HTTP_OK);
        }
        return $response;

    }

}

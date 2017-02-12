<?php

namespace Creios\Creiwork\Framework\Router;

use Creios\Creiwork\Framework\Result\Abstracts\AbstractFileResult;
use Creios\Creiwork\Framework\Result\ApacheFileResult;
use Creios\Creiwork\Framework\Result\CsvResult;
use Creios\Creiwork\Framework\Result\FileResult;
use Creios\Creiwork\Framework\Result\Interfaces\DisposableResultInterface;
use Creios\Creiwork\Framework\Result\Interfaces\StatusCodeResultInterface;
use Creios\Creiwork\Framework\Result\NginxFileResult;
use Creios\Creiwork\Framework\Result\NoContentResult;
use Creios\Creiwork\Framework\Result\RedirectResult;
use Creios\Creiwork\Framework\Result\SerializableResult;
use Creios\Creiwork\Framework\Result\StreamResult;
use Creios\Creiwork\Framework\Result\StringResult;
use Creios\Creiwork\Framework\Result\TemplateResult;
use Creios\Creiwork\Framework\Result\Util\Result;
use Creios\Creiwork\Framework\StatusCodes;
use GuzzleHttp\Psr7\Response;
use JMS\Serializer\Serializer;
use League\Plates\Engine;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use TimTegeler\Routerunner\Processor\PostProcessorInterface;

/**
 * Class PostProcessor
 * @package Creios\Creiwork\Framework\Router
 */
class PostProcessor implements PostProcessorInterface
{

    /**
     * @var Engine
     */
    protected $templateEngine;
    /**
     * @var Serializer
     */
    protected $serializer;
    /**
     * @var ServerRequestInterface
     */
    protected $serverRequest;

    /**
     * OutputLayer constructor.
     * @param Serializer $serializer
     * @param Engine $templateEngine
     */
    public function __construct(Serializer $serializer, Engine $templateEngine)
    {
        $this->serializer = $serializer;
        $this->templateEngine = $templateEngine;
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @param Result|string $output
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $serverRequest, $output)
    {
        $this->serverRequest = $serverRequest;

        $response = (new Response())->withProtocolVersion('1.1');

        if ($output instanceof DisposableResultInterface) {
            $response = $this->modifyResponseForDisposableResult($response, $output);
        }

        if ($output instanceof TemplateResult) {
            $response = $this->modifyResponseForTemplateResult($response, $output);
        } else if ($output instanceof SerializableResult) {
            $response = $this->modifyResponseForSerializableResult($response, $output);
        } else if ($output instanceof NoContentResult) {
            $response = $this->modifyResponseForNoContentResult($response, $output);
        } else if ($output instanceof RedirectResult) {
            $response = $this->modifyResponseForRedirectResult($response, $output);
        } elseif ($output instanceof FileResult) {
            $response = $this->modifyResponseForFileResult($response, $output);
        } elseif ($output instanceof ApacheFileResult) {
            $response = $this->modifyResponseForWebServerFileResult($response, $output, 'X-Sendfile');
        } elseif ($output instanceof NginxFileResult) {
            $response = $this->modifyResponseForWebServerFileResult($response, $output, 'X-Accel-Redirect');
        } elseif ($output instanceof StreamResult) {
            $response = $this->modifyResponseForStreamResult($response, $output);
        } elseif ($output instanceof StringResult) {
            $response = $this->modifyResponseForStringResult($response, $output);
        } elseif ($output instanceof CsvResult) {
            $response = $this->modifyResponseForCsvResult($response, $output);
        } else {
            $response = $this->modifyResponseForStringResult($response, StringResult::createPlainTextResult($output));
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
     * @param SerializableResult $serializableResult
     * @return ResponseInterface
     */
    private function modifyResponseForSerializableResult(ResponseInterface $response, SerializableResult $serializableResult)
    {
        if ($this->serverRequest->hasHeader('Accept')) {
            $mimeType = $this->serverRequest->getHeaderLine('Accept');
        } else if ($serializableResult->getMimeType()) {
            $mimeType = $serializableResult->getMimeType();
        } else {
            $mimeType = 'application/json';
        }
        switch ($mimeType) {
            case 'text/plain':
                $payload = print_r($serializableResult->getData(), true);
                break;
            case 'text/xml':
                $payload = $this->serializer->serialize($serializableResult->getData(), 'xml');
                break;
            case 'text/html':
                $data = $this->serializer->serialize($serializableResult->getData(), 'json');
                $this->templateEngine->addFolder('creiwork', __DIR__ . '/../Template');
                $payload = $this->templateEngine->render(
                    'creiwork::serializableResult',
                    [
                        'data' => $data,
                        'request' => $this->serverRequest
                    ]);
                break;
            case 'application/json':
            default:
                $payload = $this->serializer->serialize($serializableResult->getData(), 'json');
                break;
        }
        $stream = \GuzzleHttp\Psr7\stream_for($payload);
        $response = $this->modifyResponseWithContentLength($response, $stream);
        return $response->withHeader('Content-Type', $mimeType)->withBody($stream);
    }

    /**
     * @param ResponseInterface $response
     * @param NoContentResult $output
     * @return ResponseInterface
     */
    private function modifyResponseForNoContentResult(ResponseInterface $response, NoContentResult $output)
    {
        return $response->withStatus(StatusCodes::HTTP_NO_CONTENT);
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

<?php

namespace Creios\Creiwork\Framework\Router;

use Creios\Creiwork\Framework\Provider\SharedDataProvider;
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
use League\Plates\Engine;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
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
     * @var SharedDataProvider
     */
    private $sharedDataProvider;

    /**
     * OutputLayer constructor.
     * @param Serializer $serializer
     * @param Engine $templateEngine
     * @param SharedDataProvider $sharedDataProvider
     */
    public function __construct(SerializerInterface $serializer,
                                Engine $templateEngine,
                                SharedDataProvider $sharedDataProvider)

    {
        $this->serializer = $serializer;
        $this->templateEngine = $templateEngine;
        $this->sharedDataProvider = $sharedDataProvider;
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @param Result|string $output
     * @return ResponseInterface
     * @throws \InvalidArgumentException
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
            $response = $this->modifyResponseForNoContentResult($response);
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
     * @throws \InvalidArgumentException
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
     * @throws \InvalidArgumentException
     */
    private function modifyResponseForTemplateResult(ResponseInterface $response, TemplateResult $templateResult)
    {
        if ($this->sharedDataProvider->hasData()) {
            $this->templateEngine->addData($this->sharedDataProvider->getData());
        }
        $this->templateEngine->addData(['hostWithProtocol' => (isset($this->serverRequest->getServerParams()['HTTPS']) ? 'https://' : 'http://') . $this->serverRequest->getServerParams()['HTTP_HOST'] . '/']);
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
     * @throws \InvalidArgumentException
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
     * @throws \InvalidArgumentException
     */
    private function modifyResponseForSerializableResult(ResponseInterface $response, SerializableResult $serializableResult)
    {
        $mimeType = $serializableResult->getMimeType() ? $serializableResult->getMimeType() : 'application/json';

        switch ($mimeType) {
            case 'text/xml':
                $payload = $this->serializer->serialize($serializableResult->getData(), 'xml');
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
     * @return ResponseInterface
     * @throws \InvalidArgumentException
     * @internal param NoContentResult $output
     */
    private function modifyResponseForNoContentResult(ResponseInterface $response)
    {
        return $response->withStatus(StatusCodes::HTTP_NO_CONTENT);
    }

    /**
     * @param ResponseInterface $response
     * @param RedirectResult $redirectResult
     * @return ResponseInterface
     * @throws \InvalidArgumentException
     */
    private function modifyResponseForRedirectResult(ResponseInterface $response, RedirectResult $redirectResult)
    {
        if ($redirectResult->getUrl() === null) {
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
     * @throws \InvalidArgumentException
     */
    private function modifyResponseForFileResult(ResponseInterface $response, FileResult $fileResult)
    {
        if ($fileResult->getMimeType() !== null) {
            $mimeType = $fileResult->getMimeType();
        } else {
            $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->file($fileResult->getPath());
        }
        $stream = \GuzzleHttp\Psr7\stream_for(fopen($fileResult->getPath(), 'rb'));
        $response = $this->modifyResponseWithContentLength($response, $stream);
        return $response->withHeader('Content-Type', $mimeType)->withBody($stream);
    }

    /**
     * @param ResponseInterface $response
     * @param AbstractFileResult $result
     * @param string $redirectHeaderKey
     * @return ResponseInterface
     * @throws \InvalidArgumentException
     */
    private function modifyResponseForWebServerFileResult(ResponseInterface $response, AbstractFileResult $result, $redirectHeaderKey)
    {
        $mimeType = 'application/octet-stream';
        if ($result->getMimeType() !== null) {
            $mimeType = $result->getMimeType();
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
     * @throws \InvalidArgumentException
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
     * @throws \InvalidArgumentException
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
     * @throws \InvalidArgumentException
     */
    private function modifyResponseForCsvResult(
        ResponseInterface $response,
        CsvResult $csvResult
    )
    {
        $resource = fopen('php://temp', 'rb+');
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
     * @throws \InvalidArgumentException
     */
    private function modifyResponseForStatusCodeResult(ResponseInterface $response, StatusCodeResultInterface $statusCodeResult)
    {
        if ($statusCodeResult->getStatusCode() !== null) {
            $response = $response->withStatus($statusCodeResult->getStatusCode());
        } else {
            $response = $response->withStatus(StatusCodes::HTTP_OK);
        }
        return $response;

    }

}

<?php

namespace Creios\Creiwork\Framework;

use Creios\Creiwork\Framework\Result\DownloadableResult;
use Creios\Creiwork\Framework\Result\FileResult;
use Creios\Creiwork\Framework\Result\JsonResult;
use Creios\Creiwork\Framework\Result\RedirectResult;
use Creios\Creiwork\Framework\Result\Result;
use Creios\Creiwork\Framework\Result\StringBufferResult;
use Creios\Creiwork\Framework\Result\TemplateResult;
use GuzzleHttp\Psr7\Response;
use League\Plates\Engine;
use Psr\Http\Message\ResponseInterface;
use TimTegeler\Routerunner\PostProcessor\PostProcessorInterface;
use Zumba\Util\JsonSerializer;

/**
 * Class ResponseBuilder
 * @package Creios\Creiwork\Util
 */
class ResponseBuilder implements PostProcessorInterface
{

    /** @var  Engine */
    protected $engine;
    /**
     * @var JsonSerializer
     */
    protected $jsonSerializer;

    /**
     * OutputLayer constructor.
     * @param JsonSerializer $jsonSerializer
     * @param Engine $engine
     */
    public function __construct(JsonSerializer $jsonSerializer, Engine $engine)
    {
        $this->jsonSerializer = $jsonSerializer;
        $this->engine = $engine;
    }

    /**
     * @param Result $output
     * @return Response
     */
    public function process($output)
    {
        $response = (new Response())->withProtocolVersion('1.1');

        if ($output instanceof DownloadableResult) {
            $response = $this->modifyResponseForDownloadableResult($response, $output);
        }

        if ($output instanceof TemplateResult) {
            $response = $this->modifyResponseForTemplateResult($response, $output);
        } else if ($output instanceof JsonResult) {
            $response = $this->modifyResponseForJsonResult($response, $output);
        } else if ($output instanceof RedirectResult) {
            $response = $response->withHeader('Location', $output->getUrl());
        } elseif ($output instanceof FileResult) {
            $response = $this->modifyResponseForFileResult($response, $output);
        } elseif ($output instanceof StringBufferResult) {
            $response = $this->modifyResponseForStringBufferResult($response, $output);
        } else {
            $response = $this->modifyResponseForPlain($response, $output);
        }

        return $response;
    }

    /**
     * @param ResponseInterface $response
     * @param DownloadableResult $downloadableResult
     * @return ResponseInterface
     */
    private function modifyResponseForDownloadableResult(ResponseInterface $response, DownloadableResult $downloadableResult)
    {
        if ($downloadableResult->getFilename()) {
            $response = $response->withHeader('Content-Disposition', 'attachment; filename=' . $downloadableResult->getFilename());
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
        $stream = \GuzzleHttp\Psr7\stream_for($this->engine->render($templateResult->getTemplate(), $templateResult->getData()));
        return $response->withHeader('Content-Type', 'text/html')->withBody($stream);
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
        return $response->withHeader('Content-Type', 'application/json')->withBody($stream);
    }

    /**
     * @param ResponseInterface $response
     * @param FileResult $fileResult
     * @return ResponseInterface
     */
    private function modifyResponseForFileResult(ResponseInterface $response, FileResult $fileResult)
    {
        $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->file($fileResult->getPath());
        return $response->withHeader('Content-Type', $mimeType)
            ->withBody(\GuzzleHttp\Psr7\stream_for(fopen($fileResult->getPath(), 'r')));
    }

    /**
     * @param ResponseInterface $response
     * @param StringBufferResult $stringBufferResult
     * @return ResponseInterface
     */
    private function modifyResponseForStringBufferResult(ResponseInterface $response, StringBufferResult $stringBufferResult)
    {
        $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->buffer($stringBufferResult->getBuffer());
        return $response->withHeader('Content-Type', $mimeType)
            ->withBody(\GuzzleHttp\Psr7\stream_for($stringBufferResult->getBuffer()));
    }

    /**
     * @param ResponseInterface $response
     * @param string $output
     * @return ResponseInterface
     */
    private function modifyResponseForPlain(ResponseInterface $response, $output)
    {
        $stream = \GuzzleHttp\Psr7\stream_for($output);
        return $response->withHeader('Content-Type', 'text/plain')->withBody($stream);
    }

}

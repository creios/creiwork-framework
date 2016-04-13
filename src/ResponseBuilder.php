<?php
namespace Creios\Creiwork\Framework;

use Creios\Creiwork\Framework\Result\FileDownloadResult;
use Creios\Creiwork\Framework\Result\JsonResult;
use Creios\Creiwork\Framework\Result\RedirectResult;
use Creios\Creiwork\Framework\Result\Result;
use Creios\Creiwork\Framework\Result\StringBufferResult;
use Creios\Creiwork\Framework\Result\TemplateResult;
use GuzzleHttp\Psr7\Response;
use League\Plates\Engine;
use TimTegeler\Routerunner\PostProcessor\PostProcessorInterface;

/**
 * Class ResponseBuilder
 * @package Creios\Creiwork\Util
 */
class ResponseBuilder implements PostProcessorInterface
{

    /** @var  Engine */
    protected $engine;

    /**
     * OutputLayer constructor.
     * @param Engine $engine
     */
    public function __construct(Engine $engine)
    {
        $this->engine = $engine;
    }

    /**
     * @param Result $output
     * @return Response
     */
    public function process($output)
    {
        $response = (new Response())->withProtocolVersion('1.1');

        if ($output instanceof TemplateResult) {
            $stream = \GuzzleHttp\Psr7\stream_for($this->engine->render($output->getTemplate(), $output->getData()));

            $response = $response->withHeader('Content-Type', 'text/html')
                ->withBody($stream);

        } else if ($output instanceof JsonResult) {
            $stream = \GuzzleHttp\Psr7\stream_for(json_encode($output->getData()));

            $response = $response->withHeader('Content-Type', 'application/json')
                ->withBody($stream);

        } else if ($output instanceof RedirectResult) {
            $response = $response->withHeader('Location', $output->getUrl());

        } elseif ($output instanceof FileDownloadResult) {
            $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->file($output->getPath());
            $response = $response->withHeader('Content-Type', $mimeType)
                ->withBody(\GuzzleHttp\Psr7\stream_for(fopen($output->getPath(), 'r')));

        } elseif ($output instanceof StringBufferResult) {
            $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->buffer($output->getBuffer());
            $response = $response->withHeader('Content-Type', $mimeType)
                ->withBody(\GuzzleHttp\Psr7\stream_for($output->getBuffer()));
        } else {
            $stream = \GuzzleHttp\Psr7\stream_for($output);
            $response = $response->withHeader('Content-Type', 'text/plain')->withBody($stream);
        }

        return $response;
    }

}

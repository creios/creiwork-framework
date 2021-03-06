<?php

namespace Creios\Creiwork\Framework\Middleware;

use Creios\Creiwork\Framework\Message\Factory\ErrorFactory;
use Creios\Creiwork\Framework\Result\SerializableResult;
use Creios\Creiwork\Framework\Router\PostProcessor;
use Creios\Creiwork\Framework\StatusCodes;
use Noodlehaus\Config;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ExceptionHandlingMiddleware implements ExceptionHandlingMiddlewareInterface
{

    /** @var PostProcessor */
    private $postProcessor;
    /** @var Config */
    private $config;

    /**
     * ProductionErrorHandlingMiddleware constructor.
     * @param PostProcessor $postProcessor
     * @param Config $config
     */
    public function __construct(PostProcessor $postProcessor, Config $config)
    {
        $this->postProcessor = $postProcessor;
        $this->config = $config;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $requestHandler
     *
     * @return ResponseInterface
     * @throws \InvalidArgumentException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $requestHandler): ResponseInterface
    {
        try {
            return $requestHandler->handle($request);
        } catch (\Throwable $exception) {
            $result = $this->buildResult();
            return $this->postProcessor->process($request, $result);
        }
    }

    /**
     * @return SerializableResult
     */
    protected function buildResult(): SerializableResult
    {
        $error = $this->buildError();
        return (new SerializableResult($error))->withStatusCode(StatusCodes::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @return \Creios\Creiwork\Framework\Message\Error
     */
    protected function buildError(): \Creios\Creiwork\Framework\Message\Error
    {
        return (new ErrorFactory($this->getSupportContact()))->buildError('Unfortunately an error occurred');
    }

    /**
     * @return string
     */
    private function getSupportContact(): string
    {
        return $this->config->get('support-contact');
    }

}

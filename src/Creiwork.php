<?php

namespace Creios\Creiwork\Framework;

use Aura\Session\SegmentInterface;
use Aura\Session\Session;
use Aura\Session\SessionFactory;
use DI\Container;
use DI\ContainerBuilder;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\StreamWrapper;
use Interop\Container\ContainerInterface;
use League\Plates;
use mindplay\middleman\ContainerResolver;
use mindplay\middleman\Dispatcher;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Noodlehaus\Config;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use TimTegeler\Routerunner\Routerunner;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use function DI\factory;
use function DI\object;

/**
 * Class Creiwork
 * @package Creios\Creiwork\Framework
 */
class Creiwork
{
    /** @var Container */
    private $container;
    /** @var string */
    private $configPath;
    /** @var string */
    private $configDirectory;

    /**
     * Creiwork constructor.
     * @param string $configPath
     */
    public function __construct($configPath)
    {
        $this->configPath = $configPath;
        $this->configDirectory = dirname($this->configPath) . '/';
        $this->config = new Config($configPath);
        $this->container = $this->buildContainer();
    }

    /**
     * @return Container
     */
    private function buildContainer()
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions($this->di());
        return $containerBuilder->build();
    }

    /**
     * @return array
     */
    private function di()
    {
        return [

            Routerunner::class => function (ContainerInterface $container) {
                $routerunner = new Routerunner($this->getRouterConfigFile(), $container);
                $routerunner->setPostProcessor($container->get(ResponseBuilder::class));
                return $routerunner;
            },

            Plates\Engine::class => function () {
                return new Plates\Engine($this->getTemplateDirectory());
            },

            LoggerInterface::class => function (StreamHandler $streamHandler) {
                $logger = new Logger('Creiwork');
                $logger->pushHandler($streamHandler);
                return $logger;
            },

            StreamHandler::class => function () {
                return new StreamHandler($this->getLoggerDirectory() . '/info.log', Logger::INFO);
            },

            ServerRequestInterface::class => factory([ServerRequest::class, 'fromGlobals']),

            SessionFactory::class => object()->constructor(),

            Session::class => function (SessionFactory $sessionFactory) {
                return $sessionFactory->newInstance($_COOKIE);
            },

            SegmentInterface::class => function (Session $session) {
                return $session->getSegment('Creios\Creiwork');
            },

        ];
    }

    /**
     * @return string
     */
    private function getRouterConfigFile()
    {
        return $this->generateFilePath($this->config->get('router-config'));
    }

    /**
     * @param $filePath
     * @return string
     */
    private function generateFilePath($filePath)
    {
        return realpath($this->configDirectory . $filePath);
    }

    /**
     * @return string
     */
    private function getTemplateDirectory()
    {
        return $this->generateFilePath($this->config->get('template-dir'));
    }

    /**
     * @return string
     */
    private function getLoggerDirectory()
    {
        return $this->generateFilePath($this->config->get('logger-dir'));
    }

    public function start()
    {
        $this->pre();

        ob_start();

        if ($this->config->get('debug')) {
            $whoops = $this->container->get(Run::class);
            $whoops->pushHandler($this->container->get(PrettyPageHandler::class));
            $whoops->register();
        }

        $request = $this->container->get(ServerRequestInterface::class);
        $response = (new Dispatcher(
            [
                Routerunner::class
            ],
            new ContainerResolver($this->container))
        )->dispatch($request);

        ob_end_clean();

        $this->out($response);
    }

    private function pre()
    {
        date_default_timezone_set('UTC');
    }

    /**
     * @param ResponseInterface $response
     */
    private function out(ResponseInterface $response)
    {
        header(sprintf('HTTP/%s %s %s', $response->getProtocolVersion(), $response->getStatusCode(), $response->getReasonPhrase()));

        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }

        stream_copy_to_stream(StreamWrapper::getResource($response->getBody()), fopen('php://output', 'w'));
    }

}
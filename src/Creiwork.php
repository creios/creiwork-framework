<?php

namespace Creios\Creiwork\Framework;

use Aura\Session\SegmentInterface;
use Aura\Session\Session;
use Aura\Session\SessionFactory;
use DI\Container;
use DI\ContainerBuilder;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\StreamWrapper;
use Interop\Container\ContainerInterface;
use League\Plates;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Noodlehaus\Config;
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
        $this->container = $this->buildContainer();
    }

    private function pre()
    {
        date_default_timezone_set('UTC');
        session_start();
    }

    public function start()
    {
        $this->pre();

        $config = $this->container->get(Config::class);

        if ($config->get('debug')) {
            $whoops = $this->container->get(Run::class);
            $whoops->pushHandler($this->container->get(PrettyPageHandler::class));
            $whoops->register();
        }

        $request = $this->container->get(ServerRequestInterface::class);
        $router = $this->container->get(Routerunner::class);
        $response = $router->process($request);

        ob_end_clean();
        $this->out($response);
    }

    /**
     * @param Response $response
     */
    private function out(Response $response)
    {
        header(sprintf('HTTP/%s %s %s', $response->getProtocolVersion(), $response->getStatusCode(), $response->getReasonPhrase()));

        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }

        stream_copy_to_stream(StreamWrapper::getResource($response->getBody()), fopen('php://output', 'w'));
    }

    /**
     * @return array
     */
    private function di()
    {
        return [

            Routerunner::class => function (ContainerInterface $container, Config $config) {
                $routerunner = new Routerunner($this->generateFilePath($config->get('router-config')), $container);
                $routerunner->setPostProcessor($container->get(ResponseBuilder::class));
                return $routerunner;
            },

            Plates\Engine::class => function (Config $config) {
                return new Plates\Engine($this->generateFilePath($config->get('template-dir')));
            },

            LoggerInterface::class => function (StreamHandler $streamHandler) {
                $logger = new Logger('Creiwork');
                $logger->pushHandler($streamHandler);
                return $logger;
            },

            StreamHandler::class => function (Config $config) {
                return new StreamHandler($this->generateFilePath($config->get('logger-dir') . '/info.log'), Logger::INFO);
            },

            ServerRequestInterface::class => factory([ServerRequest::class, 'fromGlobals']),

            SessionFactory::class => object()->constructor(),

            Session::class => function (SessionFactory $sessionFactory) {
                return $sessionFactory->newInstance($_COOKIE);
            },

            SegmentInterface::class => function (Session $session) {
                return $session->getSegment('Creios\Creiwork');
            },

            Config::class => object()->constructor($this->configPath)
        ];
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
     * @param $filePath
     * @return string
     */
    private function generateFilePath($filePath)
    {
        return $this->configDirectory . $filePath;
    }

}
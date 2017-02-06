<?php

namespace Creios\Creiwork\Framework;

use Aura\Session\SegmentInterface;
use Aura\Session\Session;
use Aura\Session\SessionFactory;
use Creios\Creiwork\Framework\Exception\ConfigException;
use DI\Container;
use DI\ContainerBuilder;
use DI\Definition\Source\DefinitionSource;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\StreamWrapper;
use Interop\Container\ContainerInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use League\Plates;
use mindplay\middleman\ContainerResolver;
use mindplay\middleman\Dispatcher;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Noodlehaus\Config;
use Psr\Http\Message\ResponseInterface;
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
    /** @var string */
    const routerConfigKey = 'router-config';
    /** @var string */
    const modelDirectoryKey = 'model-dir';
    /** @var string */
    const loggerDirKey = 'logger-dir';
    /** @var string */
    const templateDirKey = 'template-dir';
    /** @var Container */
    private $container;
    /** @var string */
    private $configFilePath;
    /** @var string */
    private $configDirectoryPath;
    /** @var Config */
    private $config;
    /** @var ContainerBuilder */
    private $containerBuilder;
    /** @var array */
    private $middlewareStack;

    /**
     * Creiwork constructor.
     * @param string $configPath
     */
    public function __construct($configPath)
    {
        $this->configFilePath = $configPath;
        $this->configDirectoryPath = dirname($configPath) . '/';
        $this->containerBuilder = new ContainerBuilder();
        //add standard definitions
        $this->containerBuilder->addDefinitions($this->standardDiDefinitions());
        //add standard middleware stack
        $this->middlewareStack = $this->standardMiddlewareStack();
    }

    /**
     * @return array
     */
    private function standardDiDefinitions()
    {
        return [

            Config::class => function () {
                return new Config($this->configFilePath);
            },

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

            SessionFactory::class => object()->constructor(),

            Session::class => function (SessionFactory $sessionFactory) {
                return $sessionFactory->newInstance($_COOKIE);
            },

            SegmentInterface::class => function (Session $session) {
                return $session->getSegment('Creios\Creiwork');
            },

            SerializerBuilder::class => factory([SerializerBuilder::class, 'create']),

            Serializer::class => function (SerializerBuilder $serializerBuilder) {
                return $serializerBuilder->addMetadataDir($this->getModelDirectory())->build();
            },

        ];
    }

    /**
     * @param $filePath
     * @return string
     */
    private function generateFilePath($filePath)
    {
        return realpath($this->configDirectoryPath . $filePath);
    }

    /**
     * @return string
     */
    private function getRouterConfigFile()
    {
        return $this->generateFilePath($this->config->get(self::routerConfigKey));
    }

    /**
     * @return string
     */
    private function getModelDirectory()
    {
        return $this->generateFilePath($this->config->get(self::modelDirectoryKey));
    }

    /**
     * @return string
     */
    private function getTemplateDirectory()
    {
        return $this->generateFilePath($this->config->get(self::templateDirKey));
    }

    /**
     * @return string
     */
    private function getLoggerDirectory()
    {
        return $this->generateFilePath($this->config->get(self::loggerDirKey));
    }

    /**
     * @return array
     */
    private function standardMiddlewareStack()
    {
        return [
            //Add new middleware here
            Routerunner::class
        ];
    }

    public function start()
    {
        ob_start();

        $this->preStart();

        $this->registerWhoops();

        $response = $this->dispatch();

        ob_end_clean();

        $this->out($response);
    }

    private function preStart()
    {
        //settings
        date_default_timezone_set('UTC');
        //container
        $this->container = $this->containerBuilder->build();
        //config
        $this->config = $this->container->get(Config::class);
        $this->checkConfigKey(self::routerConfigKey);
        $this->checkConfigKey(self::loggerDirKey);
        $this->checkConfigKey(self::templateDirKey);
        $this->checkConfigKey(self::modelDirectoryKey);
    }

    /**
     * @param string $key
     * @throws ConfigException
     */
    private function checkConfigKey($key)
    {
        if (!$this->config->has($key)) {
            throw new ConfigException("Config file doesn't contain '${key}''");
        }
    }

    /**
     * Method should be replaced to appropriated Middleware
     *
     * @deprecated
     */
    private function registerWhoops()
    {
        $whoops = $this->container->get(Run::class);

        if ($this->config->get('debug')) {
            $whoops->pushHandler($this->container->get(PrettyPageHandler::class));
        } else {
            $whoops->pushHandler($this->container->get(ErrorPageHandler::class));
        }

        $whoops->register();
    }

    /**
     * @return ResponseInterface
     */
    private function dispatch()
    {
        $request = ServerRequest::fromGlobals();

        $response = (new Dispatcher($this->middlewareStack, new ContainerResolver($this->container)))
            ->dispatch($request);

        return $response;
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

    /**
     * @param string|array|DefinitionSource $definitions Can be an array of definitions, the
     *                                                   name of a file containing definitions
     *                                                   or a DefinitionSource object.
     * @return $this
     */
    public function addDefinitions($definitions)
    {
        $this->containerBuilder->addDefinitions($definitions);
        return $this;
    }

    /**
     * @param $middleware
     * @return $this
     */
    public function pushMiddleware($middleware)
    {
        array_unshift($this->middlewareStack, $middleware);
        return $this;
    }
}
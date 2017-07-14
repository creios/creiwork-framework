<?php

namespace Creios\Creiwork\Framework;

use Aura\Session\SegmentInterface;
use Aura\Session\Session;
use Aura\Session\SessionFactory;
use Creios\Creiwork\Framework\Config\Validator;
use Creios\Creiwork\Framework\Exception\ConfigException;
use Creios\Creiwork\Framework\Message\Factory\ErrorFactory;
use Creios\Creiwork\Framework\Message\Factory\InformationFactory;
use Creios\Creiwork\Framework\Router\PostProcessor;
use Creios\Creiwork\Framework\Router\PreProcessor;
use DI\Container;
use DI\ContainerBuilder;
use DI\Definition\Source\DefinitionSource;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\StreamWrapper;
use Interop\Container\ContainerInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JsonSchema\Validator as JsonValidator;
use League\Plates;
use Middlewares\ContentType;
use Middlewares\Whoops as WhoopsMiddleware;
use mindplay\middleman\ContainerResolver;
use mindplay\middleman\Dispatcher;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Noodlehaus\Config;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use TimTegeler\Routerunner\Routerunner;
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
     * @param string $configDirectoryPath
     * @throws \JMS\Serializer\Exception\InvalidArgumentException
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \TimTegeler\Routerunner\Exception\ParseException
     * @throws \Noodlehaus\Exception\EmptyDirectoryException
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function __construct($configDirectoryPath)
    {
        $this->configDirectoryPath = $this->addTrailingSlashIfMissing($configDirectoryPath);
        $this->resolveConfigFilePath(getenv('ENVIRONMENT'));
        $this->containerBuilder = new ContainerBuilder();
        //add standard definitions
        $this->containerBuilder->addDefinitions($this->standardDiDefinitions());
        //add standard middleware stack
        $this->middlewareStack = $this->standardMiddlewareStack();
    }

    /**
     * @param string $path
     * @return string
     */
    private function addTrailingSlashIfMissing($path)
    {
        return sprintf('%s/', rtrim($path, '/'));
    }

    /**
     * @param string $environment
     */
    private function resolveConfigFilePath($environment)
    {
        if ($environment !== false) {
            $this->configFilePath = $this->getEnvironmentBasedConfigFilePath($environment);
        } else {
            $this->configFilePath = $this->getDefaultConfigFilePath();
        }
    }

    /**
     * @param string $environment
     * @return string
     */
    private function getEnvironmentBasedConfigFilePath($environment)
    {
        return sprintf('%s/config.%s.json', $this->configDirectoryPath, $environment);
    }

    /**
     * @return string
     */
    private function getDefaultConfigFilePath()
    {
        return sprintf('%s/config.json', $this->configDirectoryPath);

    }

    /**
     * @return array
     * @throws \JMS\Serializer\Exception\InvalidArgumentException
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \TimTegeler\Routerunner\Exception\ParseException
     * @throws \Noodlehaus\Exception\EmptyDirectoryException
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    private function standardDiDefinitions()
    {
        return [

            Config::class => function () {
                return new Config($this->configFilePath);
            },

            Routerunner::class => function (ContainerInterface $container) {
                $routerunner = new Routerunner($this->getRouterConfigFile(), $container);
                $routerunner->setPreProcessor($container->get(PreProcessor::class));
                $routerunner->setPostProcessor($container->get(PostProcessor::class));
                return $routerunner;
            },

            Plates\Engine::class => function () {
                $templateDirectory = null;
                if ($this->isTemplateDirectorySet()) {
                    $templateDirectory = $this->getTemplateDirectory();
                }
                return new Plates\Engine($templateDirectory);
            },

            LoggerInterface::class => function (HandlerInterface $handlerInterface) {
                $logger = new Logger('Creiwork');
                $logger->pushHandler($handlerInterface);
                return $logger;
            },

            ErrorFactory::class => function (Config $config) {
                return new ErrorFactory($config->get('contact'));
            },

            InformationFactory::class => function (Config $config) {
                return new InformationFactory($config->get('contact'));
            },

            HandlerInterface::class => function () {
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

            Validator::class => function (JsonValidator $jsonValidator) {
                return new Validator($jsonValidator, __DIR__ . '/Config/config-schema.json');
            },

            WhoopsMiddleware::class => function (Config $config, ErrorPageHandler $errorPageHandler) {
                if ($config->get('debug')) {
                    return new WhoopsMiddleware();
                }
                $run = new Run();
                $run->pushHandler($errorPageHandler);
                return new WhoopsMiddleware($run);
            }
        ];
    }

    /**
     * @return string
     */
    private function getRouterConfigFile()
    {
        return $this->generateFilePath($this->config->get(self::routerConfigKey));
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
     * @return bool
     */
    private function isTemplateDirectorySet()
    {
        return $this->config->has(self::templateDirKey);
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
     * @return string
     */
    private function getModelDirectory()
    {
        return $this->generateFilePath($this->config->get(self::modelDirectoryKey));
    }

    /**
     * @return array
     */
    private function standardMiddlewareStack()
    {
        return [
            //Add new middleware here
            ContentType::class,
            WhoopsMiddleware::class,
            Routerunner::class
        ];
    }

    public function start()
    {
        ob_start();

        $this->preStart();

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
        //validate config against schema
        $configValidator = $this->container->get(Validator::class);
        if ($configValidator->validate($this->configFilePath)) {
            $this->config = $this->container->get(Config::class);
        } else {
            throw new ConfigException('Config is not valid');
        }
    }

    /**
     * @return ResponseInterface
     * @throws \LogicException
     * @throws \InvalidArgumentException
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
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    private function out(ResponseInterface $response)
    {
        header(sprintf('HTTP/%s %s %s', $response->getProtocolVersion(), $response->getStatusCode(), $response->getReasonPhrase()));

        foreach ($response->getHeaders() as $name => $values) {
            /** @var string[] $values */
            /** @var string $value */
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }

        $response->getBody()->seek(0);

        stream_copy_to_stream(StreamWrapper::getResource($response->getBody()), fopen('php://output', 'wb'));
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
<?php

namespace Creios\Creiwork\Framework;

use Aura\Session\SegmentInterface;
use Aura\Session\Session;
use Aura\Session\SessionFactory;
use Creios\Creiwork\Framework\Config\ConfigValidator;
use Creios\Creiwork\Framework\Exception\ConfigException;
use Creios\Creiwork\Framework\Message\Factory\ErrorFactory;
use Creios\Creiwork\Framework\Message\Factory\InformationFactory;
use Creios\Creiwork\Framework\Middleware\ExceptionHandlingMiddleware;
use Creios\Creiwork\Framework\Middleware\ExceptionHandlingMiddlewareInterface;
use Creios\Creiwork\Framework\Router\PostProcessor;
use Creios\Creiwork\Framework\Router\PreProcessor;
use Creios\Creiwork\Framework\Util\JsonValidator;
use DI\Container;
use DI\ContainerBuilder;
use function DI\create;
use DI\Definition\Source\DefinitionSource;
use function DI\get;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\StreamWrapper;
use League\Plates;
use Middlewares\Whoops as WhoopsMiddleware;
use mindplay\middleman\ContainerResolver;
use mindplay\middleman\Dispatcher;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Noodlehaus\Config;
use Opis\JsonSchema\ValidationError;
use Opis\JsonSchema\Validator;
use phpFastCache\CacheManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use TimTegeler\Routerunner\Components\Cache;
use TimTegeler\Routerunner\Routerunner;

/**
 * Class Creiwork
 * @package Creios\Creiwork\Framework
 */
class Creiwork
{

    /** @var string */
    const routerConfigKey = 'router-config';
    /** @var string */
    const modelDirectoryKey = 'serializer-model-dir';
    /** @var string */
    const loggerDirKey = 'logger-dir';
    /** @var string */
    const templateDirKey = 'template-dir';
    /** @var string */
    const schemaDirKey = 'schema-dir';
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
     * @param $configDirectoryPath
     * @throws ConfigException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Noodlehaus\Exception\EmptyDirectoryException
     * @throws \TimTegeler\Routerunner\Exception\ParseException
     * @throws \Exception
     */
    public function __construct($configDirectoryPath)
    {
        $this->configDirectoryPath = $this->addTrailingSlashIfMissing($configDirectoryPath);
        $this->resolveConfigFilePath(getenv('ENVIRONMENT'));
        $this->containerBuilder = new ContainerBuilder();
        //add standard definitions
        $this->containerBuilder->addDefinitions($this->standardDiDefinitions());
        $this->loadConfig();
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
        return sprintf('%sconfig.%s.creiwork.json', $this->configDirectoryPath, $environment);
    }

    /**
     * @return string
     */
    private function getDefaultConfigFilePath()
    {
        return sprintf('%sconfig.creiwork.json', $this->configDirectoryPath);

    }

    /**
     * @return string Absolute path config dir with tailing slash
     */
    private function getAbsoluteConfigDirectoryPath(){
        return $this->addTrailingSlashIfMissing(realpath($this->configDirectoryPath));
    }

    /**
     * @return string Absolute path of cache dir (without tailing slash)
     */
    private function getAbsoluteCachePath(){
        return $this->getAbsoluteConfigDirectoryPath().$this->config->get('cache_dir', '../cache');
    }

    /**
     * @throws ConfigException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \InvalidArgumentException
     * @throws \Noodlehaus\Exception\EmptyDirectoryException
     */
    private function loadConfig()
    {
        $configValidator = $this->buildConfigValidator();
        if ($configValidator->validate($this->configFilePath)) {
            $this->config = new Config($this->configFilePath);
        } else {
            /** @var ValidationError[] $errors */
            $errors = $configValidator->getErrors();
            /** @var string[] $errorStrings */
            $errorStrings = [];
            foreach($errors as $error){
               $errorStrings[] = implode(" ",$error->keywordArgs()) . ' ' . $error->keyword();
            }
            $errorString = implode(", ", $errorStrings);
            throw new ConfigException('Config is not valid: ' . $errorString);
        }
    }

    /**
     * @return ConfigValidator
     */
    private function buildConfigValidator()
    {
        return new ConfigValidator(new Validator(), __DIR__ . '/../resource/config-schema.json');
    }

    /**
     * @return array
     * @throws \InvalidArgumentException
     */
    private function standardDiDefinitions()
    {
        return [

            Config::class => function () {
                return new Config($this->configFilePath);
            },

            'routerunnerMiddlewareStack' => [],

            Routerunner::class => function (Container $container) {
                $routerunner = new Routerunner($this->getRouterConfigFile(), $container);
                $routerunner->setPreProcessor($container->get(PreProcessor::class));
                $routerunnerMiddlewareStack = $container->get('routerunnerMiddlewareStack');
                foreach ($routerunnerMiddlewareStack as $middleware) {
                    $routerunner->registerMiddleware($middleware);
                }
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

            SessionFactory::class => create()->constructor(),

            Session::class => function (SessionFactory $sessionFactory) {
                return $sessionFactory->newInstance($_COOKIE);
            },

            SegmentInterface::class => function (Session $session) {
                return $session->getSegment('Creios\Creiwork');
            },

            JsonValidator::class => function(Config $config){
                return new JsonValidator($config, $this->getAbsoluteConfigDirectoryPath());
            },

            Validator::class => function() {
                return new Validator();
            },

            SerializerInterface::class => get(Serializer::class),

            Serializer::class => function(){
                $encoders = ['json' => new JsonEncoder(), 'xml' => new XmlEncoder()];
                $normalizers = [new ObjectNormalizer()];
                return new Serializer($normalizers, $encoders);
            },

            ExceptionHandlingMiddlewareInterface::class =>
                create(ExceptionHandlingMiddleware::class),

            Cache::class => function () {
                return new Cache(CacheManager::Files(['path' => $this->getAbsoluteCachePath()]), 'routerunner');
            },

            \PDO::class => function (Config $config) {
                $host = $config->get('database.host');
                $database = $config->get('database.database');
                $user = $config->get('database.user');
                $password = $config->get('database.password');

                $dsn = "mysql:dbname={$database};host={$host};charset=UTF8";
                $pdo = new \PDO($dsn, $user, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                return $pdo;
            },
            // TODO uncomment after Quarry integration
            //Database::class =>\DI\create(PdoDatabase::class),
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
        $stack = [];

        if ($this->config->get('debug')) {
            $stack[] = WhoopsMiddleware::class;
        } else {
            $stack[] = ExceptionHandlingMiddlewareInterface::class;
        }

        $stack[] = Routerunner::class;

        return $stack;
    }

    /**
     * @throws \Exception
     */
    public function start()
    {
        ob_start();

        $this->preStart();

        $response = $this->dispatch();

        ob_end_clean();

        $this->out($response);
    }

    /**
     * @throws \Exception
     */
    private function preStart()
    {
        set_error_handler(
            function ($errno, $errstr, $errfile, $errline) {
                if (!(error_reporting() & $errno)) {
                    return false;
                }
                throw new \ErrorException(
                    $errstr,
                    0,
                    $errno,
                    $errfile,
                    $errline
                );
            }
        );

        register_shutdown_function(
            function () {
                $error = error_get_last();
                if ($error !== null) {
                    http_response_code(500);
                }
            }
        );

        //settings
        date_default_timezone_set('UTC');

        $this->container = $this->containerBuilder->build();
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

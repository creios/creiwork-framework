<?php

namespace Creios\Creiwork\Framework;

use Aura\Session\SegmentInterface;
use Noodlehaus\Config;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use TimTegeler\Routerunner\Controller\ControllerInterface;

/**
 * Class BaseController
 * @package Creios\Creiwork\Controller
 */
abstract class BaseController implements ControllerInterface
{

    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var SegmentInterface
     */
    protected $session;
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var String
     */
    protected $reroutedPath;
    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * BaseController constructor.
     * @param ServerRequestInterface $serverRequest
     * @param LoggerInterface $logger
     * @param SegmentInterface $session
     * @param Config $config
     */
    public function __construct(ServerRequestInterface $serverRequest,
                                LoggerInterface $logger,
                                SegmentInterface $session,
                                Config $config)
    {
        $this->request = $serverRequest;
        $this->logger = $logger;
        $this->session = $session;
        $this->config = $config;
    }

    /**
     * @param String $reroutedPath
     */
    public function setReroutedPath($reroutedPath)
    {
        $this->reroutedPath = $reroutedPath;
    }

}
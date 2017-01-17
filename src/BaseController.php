<?php

namespace Creios\Creiwork\Framework;

use Aura\Session\SegmentInterface;
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
     * @var ServerRequestInterface
     */
    protected $request;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var SegmentInterface
     */
    protected $session;
    /**
     * @var String
     */
    protected $reroutedPath;

    /**
     * BaseController constructor.
     * @param ServerRequestInterface $serverRequest
     * @param LoggerInterface $logger
     * @param SegmentInterface $session
     */
    public function __construct(ServerRequestInterface $serverRequest, LoggerInterface $logger, SegmentInterface $session)
    {
        $this->request = $serverRequest;
        $this->logger = $logger;
        $this->session = $session;
    }

    /**
     * @param String $reroutedPath
     */
    public function setReroutedPath($reroutedPath)
    {
        $this->reroutedPath = $reroutedPath;
    }

}
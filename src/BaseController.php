<?php

namespace Creios\Creiwork\Framework;

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
     * @var String
     */
    protected $reroutedUri;

    /**
     * BaseController constructor.
     * @param ServerRequestInterface $serverRequest
     * @param LoggerInterface $logger
     */
    public function __construct(ServerRequestInterface $serverRequest, LoggerInterface $logger)
    {
        $this->request = $serverRequest;
        $this->logger = $logger;
    }

    /**
     * @param String $reroutedUri
     */
    public function setReroutedUri($reroutedUri)
    {
        $this->reroutedUri = $reroutedUri;
    }

}
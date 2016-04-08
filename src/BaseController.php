<?php

namespace Creios\Creiwork\Framework;

use Monolog\Logger;
use Psr\Http\Message\ServerRequestInterface;
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
     * @var Logger
     */
    protected $logger;

    /**
     * @var String
     */
    protected $reroutedUri;

    /**
     * BaseController constructor.
     * @param ServerRequestInterface $serverRequest
     * @param Logger $logger
     */
    public function __construct(ServerRequestInterface $serverRequest, Logger $logger)
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
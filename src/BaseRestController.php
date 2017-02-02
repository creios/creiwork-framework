<?php

namespace Creios\Creiwork\Framework;

use Aura\Session\SegmentInterface;
use JMS\Serializer\Serializer;
use Noodlehaus\Config;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseRestController
 * @package Creios\Creiwork\Framework
 */
abstract class BaseRestController extends BaseController
{

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * BaseRestController constructor.
     * @param ServerRequestInterface $serverRequest
     * @param LoggerInterface $logger
     * @param SegmentInterface $session
     * @param Config $config
     * @param Serializer $serializer
     */
    public function __construct(ServerRequestInterface $serverRequest,
                                LoggerInterface $logger,
                                SegmentInterface $session,
                                Config $config,
                                Serializer $serializer)
    {
        parent::__construct($serverRequest, $logger, $session, $config);
        $this->serializer = $serializer;
    }

    /**
     * @param String $reroutedPath
     */
    public function setReroutedPath($reroutedPath)
    {
        $this->reroutedPath = $reroutedPath;
    }

}
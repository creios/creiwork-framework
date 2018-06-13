<?php

namespace Creios\Creiwork\Framework\Controller;

use TimTegeler\Routerunner\Controller\ControllerInterface;

/**
 * Class BaseController
 * @package Creios\Creiwork\Controller
 */
abstract class BaseController implements ControllerInterface
{

    /**
     * @var String
     */
    protected $reroutedPath;

    /**
     * @param String $reroutedPath
     */
    public function setReroutedPath($reroutedPath)
    {
        $this->reroutedPath = $reroutedPath;
    }

}
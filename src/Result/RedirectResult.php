<?php

namespace Creios\Creiwork\Framework\Result;

use Creios\Creiwork\Framework\Result\Util\Result;

/**
 * Class RedirectResult
 * @package Creios\Creiwork\Util\Results
 */
class RedirectResult extends Result
{

    /**
     * @var string
     */
    protected $url;

    /**
     * RedirectResult constructor.
     * @param string|null $url
     */
    public function __construct($url = null)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

}
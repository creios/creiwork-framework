<?php

namespace Creios\Creiwork\Framework\Message\Factory;

use Creios\Creiwork\Framework\Message\Error;

/**
 * Class ErrorFactory
 * @package Creios\Creiwork\Framework\Message\Factory
 */
class ErrorFactory extends MessageFactory
{
    /** @var string */
    private $code;
    /** @var string */
    private $solution;

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @param string $solution
     */
    public function setSolution($solution)
    {
        $this->solution = $solution;
    }

    /**
     * @param string $message
     * @return Error
     */
    public function buildError($message)
    {
        return new Error($this->contact, $message, $this->code, $this->solution);
    }

}
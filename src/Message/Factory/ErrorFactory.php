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
    private $suggestion;

    /**
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @param string $suggestion
     * @return $this
     */
    public function setSuggestion($suggestion)
    {
        $this->suggestion = $suggestion;
        return $this;
    }

    /**
     * @param string $message
     * @return Error
     */
    public function buildError($message)
    {
        return new Error($this->contact, $message, $this->code, $this->suggestion);
    }

}
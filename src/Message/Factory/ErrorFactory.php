<?php

namespace Creios\Creiwork\Framework\Message\Factory;

use Creios\Creiwork\Framework\Message\Error;

/**
 * Class ErrorFactory
 * @package Creios\Creiwork\Framework\Message\Factory
 */
class ErrorFactory
{
    /** @var string */
    private $code;
    /** @var string */
    private $suggestion;
    /** @var string */
    private $contact;

    /**
     * ErrorFactory constructor.
     * @param string $contact
     */
    public function __construct($contact)
    {
        $this->contact = $contact;
    }

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
     * @param $contact
     * @return $this
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
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
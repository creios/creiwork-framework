<?php

namespace Creios\Creiwork\Framework\Message;

/**
 * Class Error
 * @package Creios\Creiwork\Framework\Message
 */
class Error extends Message
{
    /**
     * @var string
     */
    protected $error;
    /**
     * @var string
     */
    protected $code;
    /**
     * @var string
     */
    protected $suggestion;

    /**
     * Error constructor.
     * @param string $contact
     * @param string $error
     * @param string $code
     * @param string $suggestion
     */
    public function __construct($contact, $error, $code, $suggestion)
    {
        $this->contact = $contact;
        $this->error = $error;
        $this->code = $code;
        $this->suggestion = $suggestion;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getSuggestion(): string
    {
        return $this->suggestion;
    }


}
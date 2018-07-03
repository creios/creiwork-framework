<?php

namespace Creios\Creiwork\Framework\Message;

/**
 * Class Error
 * @package Creios\Creiwork\Framework\Message
 */
class Error
{
    /** @var string  */
    private $error;
    /** @var string  */
    private $code;
    /** @var string  */
    private $suggestion;
    /** @var string **/
    private $contact;

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
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getSuggestion()
    {
        return $this->suggestion;
    }

    /**
     * @return string
     */
    public function getContact()
    {
        return $this->contact;
    }




}
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
    protected $solution;

    /**
     * Error constructor.
     * @param string $contact
     * @param string $error
     * @param string $code
     * @param string $solution
     */
    public function __construct($contact, $error, $code, $solution)
    {
        $this->contact = $contact;
        $this->error = $error;
        $this->code = $code;
        $this->solution = $solution;
    }

}
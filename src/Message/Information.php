<?php

namespace Creios\Creiwork\Framework\Message;

/**
 * Class Information
 * @package Creios\Creiwork\Framework\Message
 */
class Information extends Message
{

    /** @var string */
    protected $information;

    /**
     * Information constructor.
     * @param string $contact
     * @param string $information
     */
    public function __construct($contact, $information)
    {
        $this->contact = $contact;
        $this->information = $information;
    }

}
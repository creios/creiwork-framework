<?php

namespace Creios\Creiwork\Framework\Message;

/**
 * Class Information
 * @package Creios\Creiwork\Framework\Message
 */
class Information
{

    /** @var string */
    private $information;
    /** @var string  */
    private $contact;

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

    /**
     * @return string
     */
    public function getInformation()
    {
        return $this->information;
    }

    /**
     * @return string
     */
    public function getContact()
    {
        return $this->contact;
    }


}
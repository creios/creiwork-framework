<?php

namespace Creios\Creiwork\Framework\Message\Factory;

/**
 * Class MessageFactory
 * @package Creios\Creiwork\Framework\Message\Factory
 */
abstract class MessageFactory
{

    /**
     * @var string
     */
    protected $contact;

    /**
     * ErrorFactory constructor.
     * @param string $contact
     */
    public function __construct($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @param string $contact
     * @return $this
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
        return $this;
    }

}
<?php

namespace Creios\Creiwork\Framework\Message\Factory;

use Creios\Creiwork\Framework\Message\Information;

/**
 * Class InformationFactory
 * @package Creios\Creiwork\Framework\Message\Factory
 */
class InformationFactory
{

    /** @var string */
    private $contact;

    public function __construct($contact)
    {
        $this->contact = $contact;
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
     * @return Information
     */
    public function buildInformation($message)
    {
        return new Information($this->contact, $message);
    }

}
<?php

namespace Creios\Creiwork\Framework\Message\Factory;

use Creios\Creiwork\Framework\Message\Information;

/**
 * Class InformationFactory
 * @package Creios\Creiwork\Framework\Message\Factory
 */
class InformationFactory extends MessageFactory
{

    /**
     * @param string $message
     * @return Information
     */
    public function buildInformation($message)
    {
        return new Information($this->contact, $message);
    }

}
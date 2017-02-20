<?php

namespace Creios\Creiwork\Framework\Message\Factory;

use Creios\Creiwork\Framework\Message\Information;

class InformationFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testBuild()
    {
        // contact and message
        $contact = "Roy Trenneman <roy.trenneman@reynholm.com>";
        $message = "This is a message!";
        $expectedInformation = new Information($contact, $message);
        $factory = new InformationFactory($contact);
        $actualInformation = $factory->buildInformation($message);
        $this->assertEquals($expectedInformation, $actualInformation);

        // contact by setter and message
        $contact = "Roy Trenneman <roy.trenneman@reynholm.com>";
        $message = "This is a message!";
        $expectedInformation = new Information($contact, $message);
        $factory = (new InformationFactory($contact))->setContact($contact);
        $actualInformation = $factory->buildInformation($message);
        $this->assertEquals($expectedInformation, $actualInformation);
    }

}

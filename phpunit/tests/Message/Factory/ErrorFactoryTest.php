<?php

namespace Creios\Creiwork\Framework\Message\Factory;

use Creios\Creiwork\Framework\Message\Error;

class ErrorFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testBuild()
    {
        // contact and message
        $contact = "Roy Trenneman <roy.trenneman@reynholm.com>";
        $message = "This is an error!";
        $expectedError = new Error($contact, $message, null, null);
        $factory = new ErrorFactory($contact);
        $actualError = $factory->buildError($message);
        $this->assertEquals($expectedError, $actualError);

        // contact by setter and message
        $contact = "Roy Trenneman <roy.trenneman@reynholm.com>";
        $message = "This is an error!";
        $expectedError = new Error($contact, $message, null, null);
        $factory = (new ErrorFactory($contact))->setContact($contact);
        $actualError = $factory->buildError($message);
        $this->assertEquals($expectedError, $actualError);

        // contact, code and message
        $contact = "Roy Trenneman <roy.trenneman@reynholm.com>";
        $message = "This is an error!";
        $code = 42;
        $expectedError = new Error($contact, $message, $code, null);
        $factory = (new ErrorFactory($contact))->setCode($code);
        $actualError = $factory->buildError($message);
        $this->assertEquals($expectedError, $actualError);

        // contact, code, suggestion and message
        $contact = "Roy Trenneman <roy.trenneman@reynholm.com>";
        $message = "This is an error!";
        $code = 42;
        $suggestion = "Did you try turning it off and on again?";
        $expectedError = new Error($contact, $message, $code, $suggestion);
        $factory = (new ErrorFactory($contact))->setCode($code)->setSuggestion($suggestion);
        $actualError = $factory->buildError($message);
        $this->assertEquals($expectedError, $actualError);
    }

}

<?php

namespace Creios\Creiwork\Framework\Requests;

class TestRequest
{
    /** @var string  */
    private $firstName;
    /** @var string  */
    private $lastName;

    /**
     * TestRequest constructor.
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct(string $firstName, string $lastName)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

}
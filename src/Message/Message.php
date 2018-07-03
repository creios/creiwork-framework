<?php

namespace Creios\Creiwork\Framework\Message;

/**
 * Class Message
 * @package Creios\Creiwork\Framework\Message
 */
abstract class Message
{

    /** @var string */
    protected $contact;

    /**
     * @return string
     */
    public function getContact(): string
    {
        return $this->contact;
    }



}
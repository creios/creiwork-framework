<?php

namespace Creios\Creiwork\Framework\Result;

/**
 * Class MimeTypeResult
 * @package Creios\Creiwork\Framework\Result
 */
abstract class MimeTypeResult
{

    /** @var string */
    protected $mimeType;

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

}
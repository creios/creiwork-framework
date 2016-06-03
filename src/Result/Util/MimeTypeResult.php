<?php

namespace Creios\Creiwork\Framework\Result\Util;

/**
 * Class MimeTypeResult
 * @package Creios\Creiwork\Framework\Result
 */
abstract class MimeTypeResult extends StatusCodeResult
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

    /**
     * @param $mimeType
     * @return $this
     */
    public function withMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
        return $this;
    }

}
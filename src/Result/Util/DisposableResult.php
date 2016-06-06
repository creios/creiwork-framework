<?php

namespace Creios\Creiwork\Framework\Result\Util;

/**
 * Trait DownloadableResult
 * @package Creios\Creiwork\Framework\Result
 */
trait DisposableResult
{
    /**
     * @var Disposition
     */
    protected $disposition;

    /**
     * @param Disposition $disposition
     * @return $this
     */
    public function withDisposition(Disposition $disposition)
    {
        $this->disposition = $disposition;
        return $this;
    }

    /**
     * @return Disposition
     */
    public function getDisposition()
    {
        return $this->disposition;
    }
}

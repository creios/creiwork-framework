<?php

namespace Creios\Creiwork\Framework\Result\Traits;

use Creios\Creiwork\Framework\Result\Util\Disposition;

/**
 * Class DisposableResult
 * @package Creios\Creiwork\Framework\Result\Traits
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

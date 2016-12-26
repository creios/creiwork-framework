<?php

namespace Creios\Creiwork\Framework\Result\Interfaces;

use Creios\Creiwork\Framework\Result\Util\Disposition;

/**
 * Interface DisposableResultInterface
 * @package Creios\Creiwork\Framework\Result\Interfaces
 */
interface DisposableResultInterface
{

    /**
     * @param Disposition $disposition
     * @return $this
     */
    public function withDisposition(Disposition $disposition);

    /**
     * @return Disposition
     */
    public function getDisposition();
}

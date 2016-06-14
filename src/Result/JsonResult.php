<?php

namespace Creios\Creiwork\Framework\Result;

use Creios\Creiwork\Framework\Result\Interfaces\DataResultInterface;
use Creios\Creiwork\Framework\Result\Interfaces\DisposableResultInterface;
use Creios\Creiwork\Framework\Result\Interfaces\StatusCodeResultInterface;
use Creios\Creiwork\Framework\Result\Traits\DataResult;
use Creios\Creiwork\Framework\Result\Traits\DisposableResult;
use Creios\Creiwork\Framework\Result\Traits\StatusCodeResult;
use Creios\Creiwork\Framework\Result\Util\Result;

/**
 * Class JsonResult
 * @package Creios\Creiwork\Util\Results
 */
class JsonResult extends Result implements DataResultInterface, StatusCodeResultInterface, DisposableResultInterface
{

    use DataResult;
    use StatusCodeResult;
    use DisposableResult;

}

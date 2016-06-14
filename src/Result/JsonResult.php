<?php

namespace Creios\Creiwork\Framework\Result;

use Creios\Creiwork\Framework\Result\Util\DataResult;
use Creios\Creiwork\Framework\Result\Util\DataResultInterface;
use Creios\Creiwork\Framework\Result\Util\DisposableResult;
use Creios\Creiwork\Framework\Result\Util\DisposableResultInterface;
use Creios\Creiwork\Framework\Result\Util\Result;
use Creios\Creiwork\Framework\Result\Util\StatusCodeResult;
use Creios\Creiwork\Framework\Result\Util\StatusCodeResultInterface;

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

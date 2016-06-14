<?php

namespace Creios\Creiwork\Framework\Result;

use Creios\Creiwork\Framework\Result\Util\DataResult;
use Creios\Creiwork\Framework\Result\Util\DataResultInterface;
use Creios\Creiwork\Framework\Result\Util\DisposableResult;
use Creios\Creiwork\Framework\Result\Util\DisposableResultInterface;
use Creios\Creiwork\Framework\Result\Util\MimeTypeResult;
use Creios\Creiwork\Framework\Result\Util\MimeTypeResultInterface;
use Creios\Creiwork\Framework\Result\Util\Result;
use Creios\Creiwork\Framework\Result\Util\StatusCodeResult;
use Creios\Creiwork\Framework\Result\Util\StatusCodeResultInterface;

/**
 * Class TemplateResult
 * @package Creios\Creiwork\Util\Results
 */
class TemplateResult extends Result implements DataResultInterface, MimeTypeResultInterface, StatusCodeResultInterface, DisposableResultInterface
{

    use DataResult;
    use MimeTypeResult;
    use StatusCodeResult;
    use DisposableResult;

    /**
     * @var string
     */
    protected $template;

    /**
     * TemplateResult constructor.
     * @param string $template
     * @param array $data
     */
    public function __construct($template, array $data = null)
    {
        $this->template = $template;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

}
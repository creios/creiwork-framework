<?php

namespace Creios\Creiwork\Framework\Result;

use Creios\Creiwork\Framework\Result\Interfaces\DataResultInterface;
use Creios\Creiwork\Framework\Result\Interfaces\DisposableResultInterface;
use Creios\Creiwork\Framework\Result\Interfaces\MimeTypeResultInterface;
use Creios\Creiwork\Framework\Result\Interfaces\StatusCodeResultInterface;
use Creios\Creiwork\Framework\Result\Traits\DataResult;
use Creios\Creiwork\Framework\Result\Traits\DisposableResult;
use Creios\Creiwork\Framework\Result\Traits\MimeTypeResult;
use Creios\Creiwork\Framework\Result\Traits\StatusCodeResult;
use Creios\Creiwork\Framework\Result\Util\Result;

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
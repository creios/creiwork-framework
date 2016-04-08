<?php

namespace Creios\Creiwork\Framework\Result;

/**
 * Class TemplateResult
 * @package Creios\Creiwork\Util\Results
 */
class TemplateResult extends DataResult
{

    /**
     * @var string
     */
    protected $template;

    /**
     * TemplateResult constructor.
     * @param array $template
     * @param array $data
     */
    public function __construct($template, array $data)
    {
        $this->template = $template;
        parent::__construct($data);
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

}
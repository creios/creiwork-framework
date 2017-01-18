<?php

namespace Creios\Creiwork\Framework;

use League\Plates\Engine;
use Whoops\Handler\Handler;

/**
 * Class ErrorPageHandler
 * @package Creios\Creiwork\Util
 */
class ErrorPageHandler extends Handler
{

    /**
     * @var Engine
     */
    protected $templates;

    /**
     * ErrorPageHandler constructor.
     * @param Engine $templates
     */
    public function __construct(Engine $templates)
    {
        $this->templates = $templates;
    }

    public function handle()
    {
        echo $this->templates->render('error', ['exception' => $this->getException()]);
    }

}

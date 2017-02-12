<?php

namespace Creios\Creiwork\Framework\Config;

use JsonSchema\Validator as JsonValidator;

/**
 * Class Validator
 * @package Creios\Creiwork\Framework\Config
 */
class Validator
{

    /** @var JsonValidator */
    protected $jsonValidator;
    /** @var array */
    protected $errors;
    /** @var string */
    private $configSchemaPath;

    /**
     * Validator constructor.
     * @param JsonValidator $jsonValidator
     * @param string $configSchemaPath
     */
    public function __construct(JsonValidator $jsonValidator, $configSchemaPath)
    {
        $this->jsonValidator = $jsonValidator;
        $this->configSchemaPath = $configSchemaPath;
    }

    /**
     * @param mixed $config
     * @return bool
     */
    public function validate($config)
    {
        $this->jsonValidator->check(
            json_decode(file_get_contents($config)),
            json_decode(file_get_contents($this->configSchemaPath))
        );

        if ($this->jsonValidator->isValid()) {
            return true;
        } else {
            $this->errors = $this->jsonValidator->getErrors();
            return false;
        }

    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

}
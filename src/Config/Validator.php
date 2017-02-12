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
    /** @var mixed */
    private $configSchema;

    /**
     * Validator constructor.
     * @param JsonValidator $jsonValidator
     * @param string $configSchemaPath
     */
    public function __construct(JsonValidator $jsonValidator, $configSchemaPath)
    {
        $this->jsonValidator = $jsonValidator;
        $this->configSchema = json_decode(file_get_contents($configSchemaPath));
    }

    /**
     * @param mixed $config
     * @return bool
     */
    public function validate($config)
    {
        $this->jsonValidator->check(
            json_decode(file_get_contents($config)),
            $this->configSchema
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
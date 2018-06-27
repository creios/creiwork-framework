<?php

namespace Creios\Creiwork\Framework\Config;

use Opis\JsonSchema\Validator as JsonValidator;

/**
 * Class ConfigValidator
 * @package Creios\Creiwork\Framework\Config
 */
class ConfigValidator
{

    /** @var JsonValidator */
    protected $jsonValidator;
    /** @var array */
    protected $errors;
    /** @var mixed */
    private $configSchema;

    /**
     * ConfigValidator constructor.
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
     * @throws \JsonSchema\Exception\ExceptionInterface
     */
    public function validate($config)
    {
        $validationResult = $this->jsonValidator->dataValidation(
            json_decode(file_get_contents($config)),
            $this->configSchema
        );

        if ($validationResult->isValid()) {
            return true;
        }

        $this->errors = $validationResult->getErrors();
        return false;

    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

}
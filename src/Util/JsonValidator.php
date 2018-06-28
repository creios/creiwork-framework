<?php

namespace Creios\Creiwork\Framework\Util;

use Noodlehaus\Config;
use Opis\JsonSchema\IFilterContainer;
use Opis\JsonSchema\IFormatContainer;
use Opis\JsonSchema\IMediaTypeContainer;
use Opis\JsonSchema\ISchemaLoader;
use Opis\JsonSchema\IValidatorHelper;
use Opis\JsonSchema\Schema;
use Opis\JsonSchema\ValidationError;
use Opis\JsonSchema\ValidationResult;
use Opis\JsonSchema\Validator;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * Class JsonValidator
 * @package Creios\Creiwork\Framework\Util
 */
class JsonValidator
{
    /** @var Validator */
    private $validator;
    /** @var Config */
    private $config;
    /** @var string */
    private $configDirectoryPath;

    /**
     * JsonValidator constructor.
     * @see Validator
     * @param Config $config
     * @param string $configDirectoryPath Absolute path to config directory
     * @param IValidatorHelper|null $helper
     * @param ISchemaLoader|null $loader
     * @param IFormatContainer|null $formats
     * @param IFilterContainer|null $filters
     * @param IMediaTypeContainer|null $media
     */
    public function __construct(Config $config,
                                string $configDirectoryPath,
                                IValidatorHelper $helper = null,
                                ISchemaLoader $loader = null,
                                IFormatContainer $formats = null,
                                IFilterContainer $filters = null,
                                IMediaTypeContainer $media = null)
    {
        $this->validator = new Validator(
            $helper,
            $loader,
            $formats,
            $filters,
            $media
        );
        $this->config = $config;
        $this->configDirectoryPath = $configDirectoryPath;
    }

    /**
     * Wrapper method for Opis\JsonSchema\ConfigValidator::dataValidation()
     * @see Validator::dataValidation()
     * @param string|object|array $data
     * @param object|string $schema
     * @param int $max_errors
     * @param ISchemaLoader|null $loader
     * @return \Opis\JsonSchema\ValidationResult
     */
    public
    function dataValidation($data,
                            $schema,
                            int $max_errors = 1,
                            ISchemaLoader $loader = null): ValidationResult
    {
        return $this->validator->dataValidation($data, $schema, $max_errors, $loader);
    }

    /**
     * Validates JSON strings against a schema.
     * @param string $json
     * @param string $schemaName Name of the .json file providing the schema (without file extension)
     * @return ValidationResult
     */
    public function validateJson(string $json, string $schemaName): ValidationResult
    {
        $schemaDir = $this->config->get('schema-dir');
        $schemaPath = $this->configDirectoryPath.$schemaDir.'/'.$schemaName.'.json';
        if(!file_exists($schemaPath)){
            throw new FileNotFoundException("File $schemaPath not found");
        }
        
        $schemaJsonString = file_get_contents($schemaPath);
        $decodedJson = json_decode($json);
        if ($decodedJson === false) {
            $schema = json_decode($schemaJsonString);
            // error decoding json
            return (new ValidationResult())
                ->addError(new ValidationError($json,
                        [],
                        [],
                        $schema,
                        "Failed to decode JSON string")
                );
        }
        return $this->dataValidation($decodedJson, $schemaJsonString);
    }

}
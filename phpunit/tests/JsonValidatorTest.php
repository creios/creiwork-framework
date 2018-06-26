<?php
/**
 * Created by PhpStorm.
 * User: creios
 * Date: 26.06.18
 * Time: 16:07
 */

namespace Creios\Creiwork\Framework;


use Opis\JsonSchema\ValidationError;
use Opis\JsonSchema\Validator;
use PHPUnit\Framework\TestCase;

class JsonValidatorTest extends TestCase
{

    public function test()
    {
        $validator = new Validator();
        $schema = <<<'JSON'
{
    "type": "string",
    "minLength": 3
}
JSON;

        /** @var ValidationResult $result */
        $result = $validator->dataValidation("abc", $schema);
        $this->assertTrue($result->isValid());

        $schema = <<<'JSON'
{
    "title": "Person",
    "type": "object",
    "properties": {
        "firstName": {
            "type": "string"
        },
        "lastName": {
            "type": "string"
        },
        "age": {
            "description": "Age in years",
            "type": "integer",
            "minimum": 0
        }
    },
    "required": ["firstName", "lastName"]
}
JSON;
        /** @var ValidationResult $result */
        $result = $validator->dataValidation("abc", $schema);
        $this->assertFalse($result->isValid());
        $this->assertEquals(ValidationError::class, get_class($error = $result->getFirstError()));

        $validTestJson = <<<'JSON'
{
    "firstName" : "Max",
    "lastName" : "Mustermann",
    "age": 42 
}
JSON;

        /** @var ValidationResult $result */
        $result = $validator->dataValidation(json_decode($validTestJson), $schema);
        $this->assertTrue($result->isValid());
        $this->assertNull($result->getFirstError());

        $validTestJson = <<<'JSON'
{
    "firstName" : "Max",
    "lastName" : "Mustermann",
    "age": 42 
}
JSON;

        /** @var ValidationResult $result */
        $result = $validator->dataValidation(json_decode($validTestJson), $schema);
        $this->assertTrue($result->isValid());
        $this->assertNull($result->getFirstError());

        $validTestJson = <<<'JSON'
{
    "firstName" : "Max",
    "age": 42 
}
JSON;

        /** @var ValidationResult $result */
        $result = $validator->dataValidation(json_decode($validTestJson), $schema);
        $this->assertFalse($result->isValid());

        $schema = <<<'JSON'
{
    "type": "object",
    "properties": {
        "firstName": {
            "type": "string"
        },
        "lastName": {
            "type": "string"
        },
        "age": {
            "description": "Age in years",
            "type": "integer",
            "minimum": 0
        }
    },
    "required": ["firstName", "lastName"]
}
JSON;

    }

}
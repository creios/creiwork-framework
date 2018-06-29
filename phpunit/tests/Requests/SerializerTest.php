<?php

namespace Creios\Creiwork\Framework\Requests;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SerializerTest extends TestCase
{
    public function test()
    {
        $encoders = array(new JsonEncoder(), new XmlEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $testRequest = new TestRequest('Max', 'Mustermann');
        $expectedResult = <<<JSON
{
    "firstName":"Max",
    "lastName":"Mustermann"
}
JSON;

        $result = $serializer->serialize($testRequest, 'json');
        $this->assertJsonStringEqualsJsonString($expectedResult, $result);

        $result = $serializer->deserialize($expectedResult, TestRequest::class, 'json');
        $this->assertEquals($testRequest, $result);
    }

}

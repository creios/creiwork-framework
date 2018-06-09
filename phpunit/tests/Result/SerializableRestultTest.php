<?php

namespace Creios\Creiwork\Framework\Result;

class SerializableRestultTest extends \PHPUnit_Framework_TestCase
{

    public function testCreatePlainTextResult()
    {
        $result = SerializableResult::createPlainTextResult('test');
        $this->assertInstanceOf(SerializableResult::class, $result);
        $this->assertEquals('test', $result->getData());
        $this->assertEquals('text/plain', $result->getMimeType());
        $this->assertNull($result->getStatusCode());
        $this->assertNull($result->getDisposition());
    }

    public function testCreateXmlResult()
    {
        $xml = <<<XML
<Kreditkarte
  Herausgeber="Xema"
  Nummer="1234-5678-9012-3456"
  Deckung="2e+6"
  Waehrung="EURO">
  <Inhaber
    Name="Mustermann"
    Vorname="Max"
    maennlich="true"
    Alter="42"
    Partner="null">
    <Hobbys>
      <Hobby>Reiten</Hobby>
      <Hobby>Golfen</Hobby>
      <Hobby>Lesen</Hobby>
    </Hobbys>
    <Kinder />
  </Inhaber>
</Kreditkarte>
XML;

        $result = SerializableResult::createXmlResult($xml);
        $this->assertInstanceOf(SerializableResult::class, $result);
        $this->assertEquals($xml, $result->getData());
        $this->assertEquals('text/xml', $result->getMimeType());
        $this->assertNull($result->getStatusCode());
        $this->assertNull($result->getDisposition());
    }

    public function testCreateHtmlResult()
    {
        $html = <<<HTML
<!DOCTYPE html>
<html>
  <head>
    <title>Titel der Webseite</title>
    <!-- weitere Kopfinformationen -->
    <!-- Kommentare werden im Browser nicht angezeigt. -->
  </head>
  <body>
    <p>Inhalt der Webseite</p>
  </body>
</html>
HTML;

        $result = SerializableResult::createHtmlResult($html);
        $this->assertInstanceOf(SerializableResult::class, $result);
        $this->assertEquals($html, $result->getData());
        $this->assertEquals('text/html', $result->getMimeType());
        $this->assertNull($result->getStatusCode());
        $this->assertNull($result->getDisposition());
    }

    public function testCreateJsonResult()
    {
        $json = <<<JSON
{
  "Herausgeber": "Xema",
  "Nummer": "1234-5678-9012-3456",
  "Deckung": 2e+6,
  "Waehrung": "EURO",
  "Inhaber": 
  {
    "Name": "Mustermann",
    "Vorname": "Max",
    "maennlich": true,
    "Hobbys": [ "Reiten", "Golfen", "Lesen" ],
    "Alter": 42,
    "Kinder": [],
    "Partner": null
  }
}
JSON;
        $result = SerializableResult::createJsonResult($json);
        $this->assertInstanceOf(SerializableResult::class, $result);
        $this->assertEquals($json, $result->getData());
        $this->assertEquals('application/json', $result->getMimeType());
        $this->assertNull($result->getStatusCode());
        $this->assertNull($result->getDisposition());
    }

    public function testCreateEncodedJsonResult()
    {

        $json = <<<JSON
{
    "Id": 123456,
    "Name": "Max Mustermann",
    "Classes": {
        "Id": 12345,
        "Name": "Info I",
        "ECTS": 12.5
    }
}
JSON;

        $testArray = array(
            "Id" => 123456,
            "Name" => "Max Mustermann",
            "Classes" => array(
                "Id" => 12345,
                "Name" => "Info I",
                "ECTS" => 12.5
            )
        );

        $this->assertEquals($json, json_encode($testArray, JSON_PRETTY_PRINT));

        $resultFromCreateEncodedJsonResult = SerializableResult::createEncodedJsonResult($testArray);

        $this->assertInstanceOf(SerializableResult::class, $resultFromCreateEncodedJsonResult);
        $this->assertEquals(
        // remove all sorts of whitespaces
            preg_replace('/\s+/', '', $json),
            preg_replace('/\s+/', '', $resultFromCreateEncodedJsonResult->getData())
        );
        $this->assertEquals('application/json', $resultFromCreateEncodedJsonResult->getMimeType());
        $this->assertNull($resultFromCreateEncodedJsonResult->getStatusCode());
        $this->assertNull($resultFromCreateEncodedJsonResult->getDisposition());

        $resultFromCreateJsonResult = SerializableResult::createJsonResult(json_encode($testArray));
        $this->assertEquals($resultFromCreateEncodedJsonResult, $resultFromCreateJsonResult);

        // test json_encode param propagation
        $resultFromCreateEncodedJsonResult = SerializableResult::createEncodedJsonResult($testArray, JSON_BIGINT_AS_STRING, 1);
        $resultFromCreateJsonResult = SerializableResult::createJsonResult(json_encode($testArray, JSON_BIGINT_AS_STRING, 1));
        $this->assertEquals($resultFromCreateJsonResult, $resultFromCreateEncodedJsonResult);

        $resultFromCreateEncodedJsonResult = SerializableResult::createEncodedJsonResult($testArray, JSON_BIGINT_AS_STRING);
        $resultFromCreateJsonResult = SerializableResult::createJsonResult(json_encode($testArray, JSON_BIGINT_AS_STRING));
        $this->assertEquals($resultFromCreateJsonResult, $resultFromCreateEncodedJsonResult);


    }
}

<?php

namespace Creios\Creiwork\Framework\Result;

use stdClass;

class StringResultTest extends \PHPUnit_Framework_TestCase
{

    public function testCreatePlainTextResult()
    {
        $result = StringResult::createPlainTextResult('test');
        $this->assertInstanceOf(StringResult::class, $result);
        $this->assertEquals('test', $result->getPlainText());
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

        $result = StringResult::createXmlResult($xml);
        $this->assertInstanceOf(StringResult::class, $result);
        $this->assertEquals($xml, $result->getPlainText());
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

        $result = StringResult::createHtmlResult($html);
        $this->assertInstanceOf(StringResult::class, $result);
        $this->assertEquals($html, $result->getPlainText());
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
        $result = StringResult::createJsonResult($json);
        $this->assertInstanceOf(StringResult::class, $result);
        $this->assertEquals($json, $result->getPlainText());
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

        $resultFromCreateEncodedJsonResult = StringResult::createEncodedJsonResult($testArray);

        $this->assertInstanceOf(StringResult::class, $resultFromCreateEncodedJsonResult);
        $this->assertEquals(
            // remove all sorts of whitespaces
            preg_replace('/\s+/', '', $json),
            preg_replace('/\s+/', '', $resultFromCreateEncodedJsonResult->getPlainText())
        );
        $this->assertEquals('application/json', $resultFromCreateEncodedJsonResult->getMimeType());
        $this->assertNull($resultFromCreateEncodedJsonResult->getStatusCode());
        $this->assertNull($resultFromCreateEncodedJsonResult->getDisposition());

        $resultFromCreateJsonResult = StringResult::createJsonResult(json_encode($testArray));
        $this->assertEquals($resultFromCreateEncodedJsonResult, $resultFromCreateJsonResult);

        // test json_encode param propagation
        $resultFromCreateEncodedJsonResult = StringResult::createEncodedJsonResult($testArray, JSON_BIGINT_AS_STRING, 1);
        $resultFromCreateJsonResult = StringResult::createJsonResult(json_encode($testArray, JSON_BIGINT_AS_STRING, 1));
        $this->assertEquals($resultFromCreateJsonResult, $resultFromCreateEncodedJsonResult);

        $resultFromCreateEncodedJsonResult = StringResult::createEncodedJsonResult($testArray, JSON_BIGINT_AS_STRING);
        $resultFromCreateJsonResult = StringResult::createJsonResult(json_encode($testArray, JSON_BIGINT_AS_STRING));
        $this->assertEquals($resultFromCreateJsonResult, $resultFromCreateEncodedJsonResult);


    }
}

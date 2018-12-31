<?php

require __DIR__ . '/../../vendor/autoload.php';

$xml = new XMLReader();

$xml->open(__DIR__ . '/phpunit_5acbcaac55f2b.xml');

function toTestSuite(XMLReader $xml): \Kiboko\Component\PHPUnitPipeline\Result\PHPUnitSuite
{
    $depth = $xml->depth;

    while ($xml->read()) {
        foreach (\Kiboko\Component\PHPUnitPipeline\Result\Error::consume($xml) as $error) {
            var_dump($error);
        }
        if ($xml->nodeType === XMLReader::END_ELEMENT) {
            //var_dump([$xml->name => $xml->value]);
        } else if ($xml->nodeType === XMLReader::SIGNIFICANT_WHITESPACE) {
            //var_dump(['@'.$xml->name => $xml->value]);
        } else {
            //var_dump([$xml->nodeType]);
        }

        if ($xml->depth < $depth) {
            //break;
        }
    }

    return new \Kiboko\Component\PHPUnitPipeline\Result\PHPUnitSuite($xml);
}

toTestSuite($xml);

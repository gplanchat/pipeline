<?php

namespace functional\Kiboko\Component\JUnitXMLFile;

use Kiboko\Component\JUnitXMLFile\JUnitReader;
use Kiboko\Component\JUnitXMLFile\Result\JUnitSuite;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    public function testSuiteResult()
    {
        $xml =<<<XML_EOF
<?xml version="1.0" encoding="UTF-8"?>
<testsuites>
  <testsuite name="functional/" tests="28" assertions="28" errors="0" failures="0" skipped="0" time="0.010475">
    <testsuite name="functional\Kiboko\Component\Phroovy\AST\AgentTest" file="/Users/gplanchat/Perso/projects/pipeline/functional/Phroovy/AST/AgentTest.php" tests="6" assertions="6" errors="0" failures="0" skipped="0" time="0.005981">
      <testcase name="testAnyAgent" class="functional\Kiboko\Component\Phroovy\AST\AgentTest" classname="functional.Kiboko.Component.Phroovy.AST.AgentTest" file="/Users/gplanchat/Perso/projects/pipeline/functional/Phroovy/AST/AgentTest.php" line="11" assertions="1" time="0.004587"/>
    </testsuite>
  </testsuite>
</testsuites>
XML_EOF;

        $result = JUnitReader::fromString($xml);

        $result->rewind();

        $this->assertInstanceOf(JUnitSuite::class, $result->current());
        $this->assertInternalType('integer', $result->key());
    }
}

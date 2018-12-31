<?php

namespace Kiboko\Component\JUnitXMLFile;

use Kiboko\Component\JUnitXMLFile\Result\JUnitCase;
use Kiboko\Component\JUnitXMLFile\Result\JUnitSuite;

final class JUnitReader implements \IteratorAggregate
{
    /**
     * @var SuiteResultIterator
     */
    private $inner;

    /**
     * @param SuiteResultIterator $inner
     */
    public function __construct(SuiteResultIterator $inner = null)
    {
        $this->inner = $inner;
    }

    public static function fromXMLReader(\XMLReader $reader)
    {
        $stream = new TokenStream($reader);

        $stream->stepAfter(new TokenConstraint(\XMLReader::ELEMENT, 'testsuites'));

        $instance = new SuiteResultIterator();

        while (!$stream->finished()) {
            $instance->appendSuite(self::buildSuite($stream));
        }

        $stream->stepAfter(new TokenConstraint(\XMLReader::END_ELEMENT, 'testsuites'));

        return new self($instance);
    }

    private static function buildSuite(TokenStream $stream): JUnitSuite
    {
        $stream->stepUntil(
            new TokenConstraint(\XMLReader::ELEMENT, 'testsuite')
        );

        $date = null;
        if (($timestamp = $stream->attribute('timestamp')) !== null) {
            $date = \DateTimeImmutable::createFromFormat(\DateTime::W3C, $timestamp);
        }

        $instance = new JUnitSuite(
            $cases = new CaseResultIterator(),
            $suites = new SuiteResultIterator(),
            $stream->attribute('name'),
            $stream->attribute('tests'),
            $stream->attribute('failures'),
            $stream->attribute('errors'),
            $stream->attribute('time'),
            $stream->attribute('disabled'),
            $stream->attribute('skipped'),
            $date,
            $stream->attribute('host'),
            $stream->attribute('id'),
            $stream->attribute('package')
        );

        $stream->stepAfter(
            new TokenConstraint(\XMLReader::ELEMENT, 'testsuite')
        );

        while (!$stream->finished()) {
            $stream->stepUntil(
                new TokenConstraint(\XMLReader::ELEMENT, 'testsuite'),
                new TokenConstraint(\XMLReader::ELEMENT, 'testcase'),
                new TokenConstraint(\XMLReader::END_ELEMENT, 'testsuite')
            );

            if ($stream->finished() ||
                $stream->assert(new TokenConstraint(\XMLReader::END_ELEMENT, 'testsuite'))
            ) {
                break;
            }

            if ($stream->assert(new TokenConstraint(\XMLReader::ELEMENT, 'testsuite'))) {
                $suites->appendSuite(self::buildSuite($stream));
                continue;
            }

            if ($stream->assert(new TokenConstraint(\XMLReader::ELEMENT, 'testcase'))) {
                $cases->appendCase(self::buildCase($stream));
                continue;
            }
        }

        $stream->stepAfter(
            new TokenConstraint(\XMLReader::ELEMENT, 'testsuite'),
            new TokenConstraint(\XMLReader::END_ELEMENT, 'testsuite')
        );

        return $instance;
    }

    private static function buildCase(TokenStream $stream): JUnitCase
    {
        $instance = new JUnitCase(
            $stream->attribute('name'),
            $stream->attribute('assertions'),
            $stream->attribute('time'),
            $stream->attribute('class')
        );

        $stream->stepAfter(
            new TokenConstraint(\XMLReader::ELEMENT, 'testcase'),
            new TokenConstraint(\XMLReader::END_ELEMENT, 'testcase')
        );

        return $instance;
    }

    /**
     * @param string $content
     *
     * @return self
     */
    public static function fromString(string $content)
    {
        $reader = new \XMLReader();
        $reader->open('data://text/xml;base64,' . base64_encode($content), 'UTF-8');

        return self::fromXMLReader($reader);
    }

    /**
     * @param string $path
     *
     * @return self
     */
    public static function fromFile(string $path)
    {
        $reader = new \XMLReader();
        $reader->open($path, 'UTF-8');

        return self::fromXMLReader($reader);
    }

    /**
     * @param string $path
     * @param string $xsdPath
     *
     * @return self
     */
    public static function fromFileWithValidation(string $path, ?string $xsdPath = null)
    {
        $reader = new \XMLReader();

        if ($xsdPath === null) {
            $xsdPath = __DIR__ . '/Resources/junit.xsd';
        }

        try {
            $reader->setSchema($xsdPath);
        } catch (\Exception $e) {
            throw new \RuntimeException('It seems libxml was compiled without schema validation, could not continue.', null, $e);
        }
        $reader->open($path, 'UTF-8');

        return self::fromXMLReader($reader);
    }

    public function getIterator()
    {
        return $this->inner;
    }
}

<?php

namespace Kiboko\Component\JUnitXMLFile\Result;

final class JUnitCase
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $assertions;

    /**
     * @var float
     */
    public $timeSpent;

    /**
     * @var string
     */
    public $className;

    /**
     * @var string
     */
    public $fileName;

    /**
     * @var int
     */
    public $line;

    /**
     * @var string[]
     */
    public $skipped;

    /**
     * @var string[]
     */
    public $errors;

    /**
     * @var string[]
     */
    public $failures;

    /**
     * @var string[]
     */
    public $systemOut;

    /**
     * @var string[]
     */
    public $systemErr;

    /**
     * TestCase constructor.
     * @param string $name
     * @param int $assertions
     * @param float $timeSpent
     * @param string $className
     * @param string $fileName
     * @param int $line
     * @param string[] $skipped
     * @param string[] $errors
     * @param string[] $failures
     * @param string[] $systemOut
     * @param string[] $systemErr
     */
    public function __construct(
        ?string $name = null,
        ?int $assertions = null,
        ?float $timeSpent = null,
        ?string $className = null,
        ?string $fileName = null,
        ?int $line = null,
        array $skipped = [],
        ?ErrorCollection $errors = null,
        ?FailureCollection $failures = null,
        array $systemOut = [],
        array $systemErr = []
    ) {
        $this->name = $name;
        $this->assertions = $assertions;
        $this->timeSpent = $timeSpent;
        $this->className = $className;
        $this->fileName = $fileName;
        $this->line = $line;
        $this->skipped = $skipped;
        $this->errors = $errors ?? new ErrorCollection();
        $this->failures = $failures ?? new FailureCollection();
        $this->systemOut = $systemOut;
        $this->systemErr = $systemErr;
    }
}

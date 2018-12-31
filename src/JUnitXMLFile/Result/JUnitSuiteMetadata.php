<?php

namespace Kiboko\Component\JUnitXMLFile\Result;

trait JUnitSuiteMetadata
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $testCount;

    /**
     * @var int
     */
    public $failureCount;

    /**
     * @var int
     */
    public $errorCount;

    /**
     * @var float
     */
    public $timeSpent;

    /**
     * @var int
     */
    public $disabledCount;

    /**
     * @var int
     */
    public $skippedCount;

    /**
     * @var \DateTimeInterface
     */
    public $executedAt;

    /**
     * @var string
     */
    public $host;

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $package;
}

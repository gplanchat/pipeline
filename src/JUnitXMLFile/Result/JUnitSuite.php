<?php

namespace Kiboko\Component\JUnitXMLFile\Result;

use Kiboko\Component\JUnitXMLFile\CaseResultIterator;
use Kiboko\Component\JUnitXMLFile\SuiteResultIterator;

final class JUnitSuite implements \RecursiveIterator
{
    use JUnitSuiteMetadata;

    /**
     * @var JUnitCase[]|CaseResultIterator
     */
    private $cases;

    /**
     * @var JUnitSuite[]|SuiteResultIterator
     */
    private $children;

    /**
     * @param CaseResultIterator  $cases
     * @param SuiteResultIterator $children
     * @param string              $name
     * @param int                 $testCount
     * @param int                 $failureCount
     * @param int                 $errorCount
     * @param float               $timeSpent
     * @param int                 $disabledCount
     * @param int                 $skippedCount
     * @param \DateTimeInterface  $executedAt
     * @param string              $host
     * @param string              $id
     * @param string              $package
     */
    public function __construct(
        ?CaseResultIterator $cases,
        ?SuiteResultIterator $children = null,
        ?string $name = null,
        ?int $testCount = null,
        ?int $failureCount = null,
        ?int $errorCount = null,
        ?float $timeSpent = null,
        ?int $disabledCount = null,
        ?int $skippedCount = null,
        ?\DateTimeInterface $executedAt = null,
        ?string $host = null,
        ?string $id = null,
        ?string $package = null
    ) {
        $this->children = $children ?? new SuiteResultIterator();
        $this->cases = $cases;

        $this->name = $name;
        $this->testCount = $testCount;
        $this->failureCount = $failureCount;
        $this->errorCount = $errorCount;
        $this->timeSpent = $timeSpent;
        $this->disabledCount = $disabledCount;
        $this->skippedCount = $skippedCount;
        $this->executedAt = $executedAt;
        $this->host = $host;
        $this->id = $id;
        $this->package = $package;
    }

    public function current()
    {
        return $this->cases->current();
    }

    public function next()
    {
        $this->cases->next();
    }

    public function key()
    {
        return $this->cases->key();
    }

    public function valid()
    {
        return $this->cases->valid();
    }

    public function rewind()
    {
        return $this->cases->rewind();
    }

    public function hasChildren()
    {
        return count($this->children);
    }

    public function getChildren()
    {
        return $this->children;
    }
}

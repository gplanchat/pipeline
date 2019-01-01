<?php

namespace functional\Kiboko\Component\Phroovy\AST;

use PHPUnit\Framework\Constraint\Constraint;
use SebastianBergmann\Diff\Differ;

final class TreeStreamHasNodes extends Constraint
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var int|string
     */
    private $indexMatch;

    /**
     * @param mixed $value
     *
     * @throws \PHPUnit\Framework\Exception
     */
    public function __construct($value)
    {
        parent::__construct();
        $this->value = $value;
        $this->indexMatch = null;
    }

    /**
     * @param mixed $expected
     * @param mixed $actual
     *
     * @return string
     */
    private function diff($expected, $actual): string
    {
        $differ = new Differ();

        return $differ->diff(
            $this->exporter->export($expected),
            $this->exporter->export($actual)
        );
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @return string
     */
    public function toString(): string
    {
        return 'contains ' . $this->exporter->export($this->value);
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param mixed $other value or object to evaluate
     *
     * @return bool
     */
    protected function matches($other): bool
    {
        return $other == $this->value;
    }

    /**
     * Returns the description of the failure
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @param mixed $other evaluated value or object
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @return string
     */
    protected function failureDescription($other): string
    {
        return \sprintf(
            'the node tree contains %s',
            $this->diff($this->value, $other)
        );
    }
}

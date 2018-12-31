<?php

namespace Kiboko\Component\JUnitXMLFile;

use Kiboko\Component\Phroovy\Lexer\NewLineFilterIterator;
use Kiboko\Component\Phroovy\Lexer\Token;

class TokenStream
{
    /**
     * @var \XMLReader
     */
    private $reader;

    /**
     * @param \XMLReader $reader
     */
    public function __construct(\XMLReader $reader)
    {
        $this->reader = $reader;
    }

    public function attribute(string $name): ?string
    {
        return $this->reader->getAttribute($name);
    }

    /**
     * Asserts the constraint matches
     *
     * @param TokenConstraint $constraint
     *
     * @return bool
     */
    public function assertOne(TokenConstraint $constraint): bool
    {
        if ($this->reader->nodeType !== $constraint->token) {
            return false;
        }

        if ($constraint->name !== null) {
            return $this->reader->localName === $constraint->name;
        }

        return true;
    }

    /**
     * Asserts the constraint matches
     *
     * @param TokenConstraint[]|iterable $constraints
     *
     * @return bool
     */
    public function assert(TokenConstraint ...$constraints): bool
    {
        foreach ($constraints as $constraint) {
            if ($this->assertOne($constraint)) {
                return true;
            }
        }

        return false;
    }

    public function finished(): bool
    {
        return $this->reader->nodeType === \XMLReader::NONE;
    }

    /**
     * Steps one token further
     */
    public function step(): void
    {
        do {
            $this->reader->read();
        } while ($this->reader->nodeType === \XMLReader::SIGNIFICANT_WHITESPACE);

        if (!$this->reader->nodeType === \XMLReader::NONE) {
            throw new \RuntimeException('Please check the file contents, maybe it is empty or in the wrong format.');
        }
    }

    /**
     * Steps further until the constraints match
     *
     * @param TokenConstraint[]|iterable $constraints
     */
    public function stepUntil(TokenConstraint ...$constraints): void
    {
        while (!$this->assert(...$constraints) && !$this->finished()) {
            $this->step();
        }
    }

    /**
     * Steps further until the constraints match
     *
     * @param TokenConstraint[]|iterable $constraints
     */
    public function stepAfter(TokenConstraint ...$constraints): void
    {
        $this->stepUntil(...$constraints);
        $this->step();
    }
}

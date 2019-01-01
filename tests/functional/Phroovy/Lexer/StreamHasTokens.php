<?php

namespace functional\Kiboko\Component\Phroovy\Lexer;

use Kiboko\Component\Phroovy\Lexer\Token;
use PHPUnit\Framework\Constraint\Constraint;

class StreamHasTokens extends Constraint
{
    /**
     * @var Token[]
     */
    private $tokenStream;

    /**
     * @var int
     */
    private $failureIteration;

    /**
     * @var array
     */
    private $failureExpectedToken;

    /**
     * @var Token
     */
    private $failureActualToken;

    /**
     * @param Token[] $tokenStream
     */
    public function __construct($tokenStream)
    {
        parent::__construct();
        if (is_array($tokenStream)) {
            $tokenStream = new \ArrayIterator($tokenStream);
        }
        $this->tokenStream = $tokenStream;
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
        if ($this->failureActualToken !== null) {
            return 'has the token ' . $this->exportConstraint($this->failureExpectedToken)
                . ' at position ' . $this->failureIteration
                . ', found ' . $this->exportToken($this->failureActualToken->token, $this->failureActualToken->value);
        }

        return 'has the token ' . $this->exportConstraint($this->failureExpectedToken)
            . ' at position ' . $this->failureIteration . ', found none';
    }

    private function exportConstraint(array $constraint)
    {
        return $this->exportToken($constraint[0], $constraint[1] ?? null);
    }

    private function exportToken(string $token, ?string $value = null)
    {
        if ($value === null) {
            return strtr('%token%(*)', [
                '%token%' => $token,
            ]);
        }

        return strtr('%token%("%value%")', [
            '%token%' => $token,
            '%value%' => $value,
        ]);
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
        $this->failureIteration = null;

        $iterator = new \MultipleIterator(\MultipleIterator::MIT_NEED_ANY);
        $iterator->attachIterator($this->tokenStream);
        if (is_array($other)) {
            $iterator->attachIterator(new \ArrayIterator($other));
        } else {
            $iterator->attachIterator($other);
        }

        /**
         * @var array $expected
         * @var Token $actual
         */
        $iteration = 0;
        foreach ($iterator as list($expected, $actual)) {
            ++$iteration;
            if ($expected === null || $actual === null) {
                $this->failureIteration = $iteration;
                $this->failureActualToken = $actual;
                $this->failureExpectedToken = $expected;
                return false;
            }

            if (!$actual instanceof Token) {
                $this->failureIteration = $iteration;
                $this->failureActualToken = $actual;
                $this->failureExpectedToken = $expected;
                return false;
            }

            if ($expected[0] !== $actual->token) {
                $this->failureIteration = $iteration;
                $this->failureActualToken = $actual;
                $this->failureExpectedToken = $expected;
                return false;
            }

            if (isset($expected[1]) && $expected[1] !== $actual->value) {
                $this->failureIteration = $iteration;
                $this->failureActualToken = $actual;
                $this->failureExpectedToken = $expected;
                return false;
            }
        }

        return true;
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
        return 'a token stream ' . $this->toString();
    }
}

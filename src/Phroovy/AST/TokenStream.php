<?php

namespace Kiboko\Component\Phroovy\AST;

use Kiboko\Component\Phroovy\Lexer\Token;

class TokenStream
{
    /**
     * @var \Iterator
     */
    private $iterator;

    /**
     * @param \Iterator $iterator
     */
    public function __construct(\Iterator $iterator)
    {
        $this->iterator = $iterator;
        $iterator->rewind();
    }

    /**
     * Check if the stream is finished
     *
     * @return bool
     */
    public function finished(): bool
    {
        return !$this->iterator->valid();
    }

    /**
     * Watch the current token
     *
     * @return Token
     */
    public function watch(): Token
    {
        if ($this->finished()) {
            throw new \RuntimeException('No more data to consume.');
        }

        return $this->iterator->current();
    }

    /**
     * Asserts the constraint matches
     *
     * @param TokenConstraint $constraint
     *
     * @return bool
     */
    public function assert(TokenConstraint $constraint): bool
    {
        $token = $this->watch();
        if ($token->token !== $constraint->token) {
            return false;
        }

        if ($constraint->value !== null) {
            return $token->value === $constraint->value;
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
    public function assertAny(iterable $constraints): bool
    {
        foreach ($constraints as $constraint) {
            if ($this->assert($constraint)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Steps one token further
     */
    public function step(): void
    {
        $this->iterator->next();
    }

    /**
     * Steps further until the constraints match
     *
     * @param TokenConstraint[]|iterable $constraints
     */
    public function stepUntil(iterable $constraints): void
    {
        while (!$this->assertAny($constraints)) {
            $this->step();
        }
    }

    /**
     * Consume the current token and step one token further
     *
     * @return Token
     */
    public function consume(): Token
    {
        $token = $this->watch();
        $this->step();

        return $token;
    }

    /**
     * @param TokenConstraint $constraint
     *
     * @throws Exception\UnexpectedTokenException
     *
     * @return Token
     */
    public function expect(TokenConstraint $constraint): Token
    {
        if ($this->assert($constraint)) {
            return $this->consume();
        }

        throw Exception\UnexpectedTokenException::unmatchedConstraint($this->watch(), $constraint);
    }

    /**
     * @param TokenConstraint[]|iterable $constraints
     *
     * @throws Exception\UnexpectedTokenException
     *
     * @return Token
     */
    public function expectAny(iterable $constraints): Token
    {
        if ($this->assertAny($constraints)) {
            return $this->consume();
        }

        throw Exception\UnexpectedTokenException::unmatchedConstraints($this->watch(), $constraints);
    }
}

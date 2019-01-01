<?php

namespace functional\Kiboko\Component\Phroovy\Lexer;

use Kiboko\Component\Phroovy\Lexer\Token;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use PHPUnit\Util\InvalidArgumentHelper;

class TestCase extends PHPUnitTestCase
{
    /**
     * Asserts that a Token stream has specified tokens.
     *
     * @param Token[] $expectedTokens
     * @param iterable $tokenStream
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertStreamHasTokens($expectedTokens, $tokenStream, string $message = ''): void
    {
        if (!(\is_array($expectedTokens) || $expectedTokens instanceof \Iterator)) {
            throw InvalidArgumentHelper::factory(
                1,
                'iterable'
            );
        }

        if (!(\is_array($tokenStream) || $tokenStream instanceof \Iterator)) {
            throw InvalidArgumentHelper::factory(
                2,
                'iterable'
            );
        }

        $constraint = new StreamHasTokens($expectedTokens);

        static::assertThat($tokenStream, $constraint, $message);
    }
}

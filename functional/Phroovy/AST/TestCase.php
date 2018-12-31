<?php

namespace functional\Kiboko\Component\Phroovy\AST;

use Kiboko\Component\Phroovy\AST\Node\NodeInterface;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use PHPUnit\Util\InvalidArgumentHelper;

abstract class TestCase extends PHPUnitTestCase
{
    public function firstElement(iterable $stream)
    {
        foreach ($stream as $item) {
            return $item;
        }

        throw new \RuntimeException('Empty iterable.');
    }

    /**
     * Asserts that a Token stream has specified tokens.
     *
     * @param NodeInterface $expected
     * @param NodeInterface $actual
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertTreeHasNode($expected, $actual, string $message = ''): void
    {
        if (!($expected instanceof NodeInterface)) {
            throw InvalidArgumentHelper::factory(
                1,
                NodeInterface::class
            );
        }

        if (!($actual instanceof NodeInterface)) {
            throw InvalidArgumentHelper::factory(
                2,
                NodeInterface::class
            );
        }

        $constraint = new TreeStreamHasNodes($expected);

        static::assertThat($actual, $constraint, $message);
    }
}

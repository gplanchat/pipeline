<?php

namespace functional\Kiboko\Component\Phroovy\AST\TreeResolution;

use functional\Kiboko\Component\Phroovy\AST\TestCase;
use Kiboko\Component\Phroovy\AST\Node\NodeInterface;
use Kiboko\Component\Phroovy\AST\Node\StepNode;
use Kiboko\Component\Phroovy\AST\TokenStream;
use Kiboko\Component\Phroovy\AST\TreeResolution\StepResolution;
use Kiboko\Component\Phroovy\Lexer\Token;

class StepResolutionTest extends TestCase
{
    public function stepDataProvider()
    {
        yield [
            [
                new Token(token::IDENTIFIER, 2, 'sh', 0, 0, 1),
                new Token(token::SINGLE_QUOTED_STRING, 19, 'echo "Hello, world"', 3, 0, 4),
            ],
            new StepNode('sh', ['echo "Hello, world"'])
        ];
    }

    /**
     * @dataProvider stepDataProvider
     */
    public function testStep(array $source, NodeInterface $expected)
    {
        $resolution = new StepResolution();

        $actual = $resolution->create(new TokenStream(new \ArrayIterator($source)));

        $this->assertEquals($expected, $actual);
    }
}

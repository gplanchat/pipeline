<?php

namespace functional\Kiboko\Component\Phroovy\AST\TreeResolution;

use functional\Kiboko\Component\Phroovy\AST\TestCase;
use Kiboko\Component\Phroovy\AST\Node\NodeInterface;
use Kiboko\Component\Phroovy\AST\Node\StaticValue\ListNode;
use Kiboko\Component\Phroovy\AST\Node\StaticValue\StringNode;
use Kiboko\Component\Phroovy\AST\Node\StepNode;
use Kiboko\Component\Phroovy\AST\TokenStream;
use Kiboko\Component\Phroovy\AST\TreeResolution\CollectionResolution;
use Kiboko\Component\Phroovy\AST\TreeResolution\HashMapResolution;
use Kiboko\Component\Phroovy\AST\TreeResolution\ListResolution;
use Kiboko\Component\Phroovy\AST\TreeResolution\StaticValueResolutionFacade;
use Kiboko\Component\Phroovy\AST\TreeResolution\StepResolution;
use Kiboko\Component\Phroovy\Lexer\Token;

class StepResolutionTest extends TestCase
{
    public function stepDataProvider()
    {
        yield [
            [
                new Token(token::IDENTIFIER, 2, 'sh', 0, 0, 1),
                new Token(token::SINGLE_QUOTED_STRING, 4, 'echo', 3, 0, 4),
                new Token(token::SINGLE_QUOTED_STRING, 12, 'Hello, world', 6, 0, 7),
                new Token(token::NEWLINE, 1, "\n", 24, 0, 25),
            ],
            new StepNode('sh', new ListNode([
                new StringNode('echo'),
                new StringNode('Hello, world')
            ]))
        ];
    }

    /**
     * @dataProvider stepDataProvider
     */
    public function testStep(array $source, NodeInterface $expected)
    {
        $facade = new StaticValueResolutionFacade();
        $facade->attach(new HashMapResolution($facade));
        $facade->attach(new CollectionResolution($facade));

        $resolution = new StepResolution($facade, new ListResolution());

        $actual = $resolution->create(new TokenStream(new \ArrayIterator($source)));

        $this->assertEquals($expected, $actual);
    }
}

<?php

namespace functional\Kiboko\Component\Phroovy\AST;

use Kiboko\Component\Phroovy\AST\Node as AST;
use Kiboko\Component\Phroovy\AST\Tree;
use Kiboko\Component\Phroovy\Lexer\Lexer;

class OptionsTest extends TestCase
{
    public function testAnyAgent()
    {
        $pipeline =<<<PIPE_EOL
pipeline {
    options {
        timeout(time: 1, unit: 'HOURS') 
    }
}
PIPE_EOL;

        $lexer = new Lexer();
        $tree = new Tree();

        $expected = new AST\PipelineNode(
            null,
            null,
            null,
            null,
            new AST\OptionsNode([
                new AST\FunctionCallNode('timeout', [
                    'time' => 1,
                    'unit' => 'HOURS',
                ])
            ])
        );

        $this->assertTreeHasNode($expected, $this->firstElement($tree->compile($lexer->tokenize($pipeline))));
    }
}

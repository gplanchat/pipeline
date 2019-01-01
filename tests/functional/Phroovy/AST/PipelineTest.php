<?php

namespace functional\Kiboko\Component\Phroovy\AST;

use Kiboko\Component\Phroovy\AST\Node\PipelineNode;
use Kiboko\Component\Phroovy\AST\Tree;
use Kiboko\Component\Phroovy\Lexer\Lexer;

class PipelineTest extends TestCase
{
    public function testEmptyPipeline()
    {
        $pipeline = <<<PIPE_EOL
pipeline {
}
PIPE_EOL;

        $lexer = new Lexer();
        $tree = new Tree();

        $expected = new PipelineNode();

        $this->assertTreeHasNode($expected, $this->firstElement($tree->compile($lexer->tokenize($pipeline))));
    }

    public function testEmptyPipelineWithMultiLineComment()
    {
        $pipeline = <<<PIPE_EOL
pipeline {
    /* insert Declarative Pipeline here */
}
PIPE_EOL;

        $lexer = new Lexer();
        $tree = new Tree();

        $expected = new PipelineNode();

        $this->assertTreeHasNode($expected, $this->firstElement($tree->compile($lexer->tokenize($pipeline))));
    }

    public function testEmptyPipelineWithSingleLineComment()
    {
        $pipeline = <<<PIPE_EOL
pipeline {
    // insert Declarative Pipeline here
}
PIPE_EOL;

        $lexer = new Lexer();
        $tree = new Tree();

        $expected = new PipelineNode();

        $this->assertTreeHasNode($expected, $this->firstElement($tree->compile($lexer->tokenize($pipeline))));
    }
}

<?php

namespace functional\Kiboko\Component\Groovy\AST;

use Kiboko\Component\Groovy\AST\Node\PipelineNode;
use Kiboko\Component\Groovy\AST\Tree;
use Kiboko\Component\Groovy\Lexer\CommentFilterIterator;
use Kiboko\Component\Groovy\Lexer\Lexer;

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

        $this->assertTreeHasNode($expected, $tree->compile(new CommentFilterIterator($lexer->tokenize($pipeline))));
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

        $this->assertTreeHasNode($expected, new CommentFilterIterator($tree->compile($lexer->tokenize($pipeline))));
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

        $this->assertTreeHasNode($expected, new CommentFilterIterator($tree->compile($lexer->tokenize($pipeline))));
    }
}

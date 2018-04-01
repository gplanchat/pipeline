<?php

namespace functional\Kiboko\Component\Groovy\AST;

use Kiboko\Component\Groovy\AST\Node\AgentNode;
use Kiboko\Component\Groovy\AST\Node\PipelineNode;
use Kiboko\Component\Groovy\AST\Tree;
use Kiboko\Component\Groovy\Lexer\CommentFilterIterator;
use Kiboko\Component\Groovy\Lexer\Lexer;

class AgentTest extends TestCase
{
    public function testAnyAgent()
    {
        $pipeline =<<<PIPE_EOL
pipeline {
    agent any
}
PIPE_EOL;

        $lexer = new Lexer();
        $tree = new Tree();

        $expected = new PipelineNode();
        $expected->agent = new AgentNode('any');

        $this->assertTreeHasNode($expected, $tree->compile(new CommentFilterIterator($lexer->tokenize($pipeline))));
    }

    public function testNamedAgent()
    {
        $pipeline =<<<PIPE_EOL
pipeline {
    agent 'docker'
}
PIPE_EOL;

        $lexer = new Lexer();
        $tree = new Tree();

        $expected = new PipelineNode();
        $expected->agent = new AgentNode('docker');

        $this->assertTreeHasNode($expected, $tree->compile(new CommentFilterIterator($lexer->tokenize($pipeline))));
    }

    public function testParameteredAgent()
    {
        $pipeline =<<<PIPE_EOL
pipeline {
    agent {
        docker {
            image 'maven:3-alpine'
            label 'my-defined-label'
            args  '-v /tmp:/tmp'
        }
    }
}
PIPE_EOL;

        $lexer = new Lexer();
        $tree = new Tree();

        $expected = new PipelineNode();
        $expected->agent = new AgentNode('docker', [
            'image' => 'maven:3-alpine',
            'label' => 'my-defined-label',
            'args'  => '-v /tmp:/tmp',
        ]);

        $this->assertTreeHasNode($expected, $tree->compile(new CommentFilterIterator($lexer->tokenize($pipeline))));
    }
}

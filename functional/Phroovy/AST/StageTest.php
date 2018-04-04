<?php

namespace functional\Kiboko\Component\Phroovy\AST;

use Kiboko\Component\Phroovy\AST\Node as AST;
use Kiboko\Component\Phroovy\AST\Tree;
use Kiboko\Component\Phroovy\Lexer\Lexer;

class StageTest extends TestCase
{
    public function testEmptyStageList()
    {
        $pipeline =<<<PIPE_EOL
pipeline {
    stages {
    }
}
PIPE_EOL;

        $lexer = new Lexer();
        $tree = new Tree();

        $expected = new AST\PipelineNode();

        $this->assertTreeHasNode($expected, $this->firstElement($tree->compile($lexer->tokenize($pipeline))));
    }

    public function testEmptyStage()
    {
        $pipeline =<<<PIPE_EOL
pipeline {
    stages {
        stage('Test') {
        }
    }
}
PIPE_EOL;

        $lexer = new Lexer();
        $tree = new Tree();

        $expected = new AST\PipelineNode(new AST\StageCollectionNode([
            new AST\StageNode('Test')
        ]));

        $this->assertTreeHasNode($expected, $this->firstElement($tree->compile($lexer->tokenize($pipeline))));
    }

    public function testEmptySteps()
    {
        $pipeline =<<<PIPE_EOL
pipeline {
    stages {
        stage('Test') {
            steps {
            }
        }
    }
}
PIPE_EOL;

        $lexer = new Lexer();
        $tree = new Tree();

        $expected = new AST\PipelineNode(new AST\StageCollectionNode([
            new AST\StageNode('Test')
        ]));

        $this->assertTreeHasNode($expected, $this->firstElement($tree->compile($lexer->tokenize($pipeline))));
    }

    public function testStageWithOneStep()
    {
        $pipeline =<<<PIPE_EOL
pipeline {
    stages {
        stage('Test') {
            steps {
                echo 'Hello, World'
            }
        }
    }
}
PIPE_EOL;

        $lexer = new Lexer();
        $tree = new Tree();

        $expected = new AST\PipelineNode(new AST\StageCollectionNode([
            new AST\StageNode('Test', new AST\StepCollectionNode([
                new AST\StepNode('echo', ['Hello, World'])
            ]))
        ]));

        $this->assertTreeHasNode($expected, $this->firstElement($tree->compile($lexer->tokenize($pipeline))));
    }

    public function testTwoStagesWithOneStep()
    {
        $pipeline =<<<PIPE_EOL
pipeline {
    stages {
        stage('Test') {
            steps {
                echo 'Hello, World'
            }
        }
        stage('Build') {
            steps {
                echo 'Hello, World'
            }
        }
    }
}
PIPE_EOL;

        $lexer = new Lexer();
        $tree = new Tree();

        $expected = new AST\PipelineNode(new AST\StageCollectionNode([
            new AST\StageNode('Test', new AST\StepCollectionNode([
                new AST\StepNode('echo', ['Hello, World']),
            ])),
            new AST\StageNode('Build', new AST\StepCollectionNode([
                new AST\StepNode('echo', ['Hello, World']),
            ])),
        ]));

        $this->assertTreeHasNode($expected, $this->firstElement($tree->compile($lexer->tokenize($pipeline))));
    }
}

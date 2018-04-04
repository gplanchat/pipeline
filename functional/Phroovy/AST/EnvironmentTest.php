<?php

namespace functional\Kiboko\Component\Phroovy\AST;

use Kiboko\Component\Phroovy\AST\Node\EnvironmentNode;
use Kiboko\Component\Phroovy\AST\Node\PipelineNode;
use Kiboko\Component\Phroovy\AST\Node\StageCollectionNode;
use Kiboko\Component\Phroovy\AST\Node\StageNode;
use Kiboko\Component\Phroovy\AST\Tree;
use Kiboko\Component\Phroovy\Lexer\Lexer;

class EnvironmentTest extends TestCase
{
    public function testRootEnvironment()
    {
        $pipeline = <<<PIPE_EOL
pipeline {
    environment { 
        CC = 'clang'
    }
}
PIPE_EOL;

        $lexer = new Lexer();
        $tree = new Tree();

        $expected = new PipelineNode(
            null,
            null,
            new EnvironmentNode([
                'CC' => 'clang'
            ])
        );

        $this->assertTreeHasNode($expected, $this->firstElement($tree->compile($lexer->tokenize($pipeline))));
    }

    public function testStageEnvironment()
    {
        $pipeline = <<<PIPE_EOL
pipeline {
    stages {
        stage('Test') {
            environment { 
                CC = 'clang'
            }
        }
    }
}
PIPE_EOL;

        $lexer = new Lexer();
        $tree = new Tree();

        $expected = new PipelineNode(
            new StageCollectionNode([
                new StageNode('Test', null, null, new EnvironmentNode([
                    'CC' => 'clang'
                ]))
            ])
        );

        $this->assertTreeHasNode($expected, $this->firstElement($tree->compile($lexer->tokenize($pipeline))));
    }
}

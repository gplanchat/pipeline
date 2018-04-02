<?php

namespace functional\Kiboko\Component\Phroovy\AST;

use Kiboko\Component\Phroovy\AST\Node as AST;
use Kiboko\Component\Phroovy\AST\Tree;
use Kiboko\Component\Phroovy\Lexer\Lexer;

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

        $expected = new AST\PipelineNode();
        $expected->agent = new AST\AnyAgentNode();

        $this->assertTreeHasNode($expected, $tree->compile($lexer->tokenize($pipeline)));
    }

    public function testNoneAgent()
    {
        $pipeline =<<<PIPE_EOL
pipeline {
    agent none
}
PIPE_EOL;

        $lexer = new Lexer();
        $tree = new Tree();

        $expected = new AST\PipelineNode();
        $expected->agent = new AST\NoneAgentNode();

        $this->assertTreeHasNode($expected, $tree->compile($lexer->tokenize($pipeline)));
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

        $expected = new AST\PipelineNode();
        $expected->agent = new AST\AgentNode('docker');

        $this->assertTreeHasNode($expected, $tree->compile($lexer->tokenize($pipeline)));
    }

    public function testParameteredAgentOnMultipleLines()
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

        $expected = new AST\PipelineNode();
        $expected->agent = new AST\AgentNode('docker', [
            'image' => 'maven:3-alpine',
            'label' => 'my-defined-label',
            'args'  => '-v /tmp:/tmp',
        ]);

        $this->assertTreeHasNode($expected, $tree->compile($lexer->tokenize($pipeline)));
    }

    public function testParameteredAgentOnSingleLine()
    {
        $pipeline =<<<PIPE_EOL
pipeline {
    agent { docker 'maven:3-alpine' }
    }
}
PIPE_EOL;

        $lexer = new Lexer();
        $tree = new Tree();

        $expected = new AST\PipelineNode();
        $expected->agent = new AST\AgentNode('docker', [
            'maven:3-alpine',
        ]);

        $this->assertTreeHasNode($expected, $tree->compile($lexer->tokenize($pipeline)));
    }

    public function testAgentOnStageLevel()
    {
        $pipeline =<<<PIPE_EOL
pipeline {
    agent none 
    stages {
        stage('Example Build') {
            agent { docker 'maven:3-alpine' }
        }
    }
}
PIPE_EOL;

        $lexer = new Lexer();
        $tree = new Tree();

        $expected = new AST\PipelineNode(
            new AST\StageCollectionNode([
                new AST\StageNode(
                    'Example Build',
                    new AST\StepCollectionNode(),
                    new AST\AgentNode('docker', [
                        'maven:3-alpine',
                    ])
                ),
            ]),
            new AST\NoneAgentNode()
        );

        $this->assertTreeHasNode($expected, $tree->compile($lexer->tokenize($pipeline)));
    }
}

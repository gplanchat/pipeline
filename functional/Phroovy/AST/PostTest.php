<?php

namespace functional\Kiboko\Component\Phroovy\AST;

use Kiboko\Component\Phroovy\AST\Node\PipelineNode;
use Kiboko\Component\Phroovy\AST\Node\PostActionNode;
use Kiboko\Component\Phroovy\AST\Node\StepCollectionNode;
use Kiboko\Component\Phroovy\AST\Node\StepNode;
use Kiboko\Component\Phroovy\AST\Tree;
use Kiboko\Component\Phroovy\Lexer\Lexer;

class PostTest extends TestCase
{
    public function testAlwaysPostAction()
    {
        $pipeline = <<<PIPE_EOL
pipeline {
    post { 
        always { 
            echo 'I will always say Hello again!'
        }
    }
}
PIPE_EOL;

        $lexer = new Lexer();
        $tree = new Tree();

        $expected = new PipelineNode(
            null,
            null,
            null,
            new PostActionNode(
                new StepCollectionNode([
                    new StepNode('echo', ['I will always say Hello again!'])
                ])
            )
        );

        $this->assertTreeHasNode($expected, $tree->compile($lexer->tokenize($pipeline)));
    }

    public function testSuccessPostAction()
    {
        $pipeline = <<<PIPE_EOL
pipeline {
    post { 
        success { 
            echo 'I will always say Hello again!'
        }
    }
}
PIPE_EOL;

        $lexer = new Lexer();
        $tree = new Tree();

        $expected = new PipelineNode(
            null,
            null,
            null,
            new PostActionNode(
                null,
                new StepCollectionNode([
                    new StepNode('echo', ['I will always say Hello again!'])
                ])
            )
        );

        $this->assertTreeHasNode($expected, $tree->compile($lexer->tokenize($pipeline)));
    }

    public function testChangedPostAction()
    {
        $pipeline = <<<PIPE_EOL
pipeline {
    post { 
        changed { 
            echo 'I will always say Hello again!'
        }
    }
}
PIPE_EOL;

        $lexer = new Lexer();
        $tree = new Tree();

        $expected = new PipelineNode(
            null,
            null,
            null,
            new PostActionNode(
                null,
                null,
                new StepCollectionNode([
                    new StepNode('echo', ['I will always say Hello again!'])
                ])
            )
        );

        $this->assertTreeHasNode($expected, $tree->compile($lexer->tokenize($pipeline)));
    }

    public function testUnstablePostAction()
    {
        $pipeline = <<<PIPE_EOL
pipeline {
    post { 
        unstable { 
            echo 'I will always say Hello again!'
        }
    }
}
PIPE_EOL;

        $lexer = new Lexer();
        $tree = new Tree();

        $expected = new PipelineNode(
            null,
            null,
            null,
            new PostActionNode(
                null,
                null,
                null,
                new StepCollectionNode([
                    new StepNode('echo', ['I will always say Hello again!'])
                ])
            )
        );

        $this->assertTreeHasNode($expected, $tree->compile($lexer->tokenize($pipeline)));
    }

    public function testFailurePostAction()
    {
        $pipeline = <<<PIPE_EOL
pipeline {
    post { 
        failure { 
            echo 'I will always say Hello again!'
        }
    }
}
PIPE_EOL;

        $lexer = new Lexer();
        $tree = new Tree();

        $expected = new PipelineNode(
            null,
            null,
            null,
            new PostActionNode(
                null,
                null,
                null,
                null,
                new StepCollectionNode([
                    new StepNode('echo', ['I will always say Hello again!'])
                ])
            )
        );

        $this->assertTreeHasNode($expected, $tree->compile($lexer->tokenize($pipeline)));
    }
}

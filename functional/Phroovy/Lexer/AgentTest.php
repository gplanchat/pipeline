<?php

namespace functional\Kiboko\Component\Phroovy\Lexer;

use Kiboko\Component\Phroovy\Lexer\Lexer;
use Kiboko\Component\Phroovy\Lexer\Token;

class AgentTest extends TestCase
{
    public function testDockerAgent()
    {
        $pipeline =<<<PIPE_EOL
agent {
    docker {
        image 'maven:3-alpine'
        label 'my-defined-label'
        args  '-v /tmp:/tmp'
    }
}
PIPE_EOL;

        $lexer = new Lexer();

        $this->assertStreamHasTokens(
            [
                [Token::KEYWORD, 'agent'],
                [Token::OPENING_CURLY_BRACES],
                [Token::IDENTIFIER, 'docker'],
                [Token::OPENING_CURLY_BRACES],
                [Token::IDENTIFIER, 'image'],
                [Token::SINGLE_QUOTED_STRING, 'maven:3-alpine'],
                [Token::IDENTIFIER, 'label'],
                [Token::SINGLE_QUOTED_STRING, 'my-defined-label'],
                [Token::IDENTIFIER, 'args'],
                [Token::SINGLE_QUOTED_STRING, '-v /tmp:/tmp'],
                [Token::CLOSING_CURLY_BRACES],
                [Token::CLOSING_CURLY_BRACES],
            ],
            $lexer->tokenize($pipeline)
        );
    }
}

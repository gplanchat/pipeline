<?php

namespace functional\Kiboko\Component\Phroovy\Lexer;

use Kiboko\Component\Phroovy\Lexer\Lexer;
use Kiboko\Component\Phroovy\Lexer\Token;

class PipelineTest extends TestCase
{
    public function testEmptyPipeline()
    {
        $pipeline = <<<PIPE_EOL
pipeline {
}
PIPE_EOL;

        $lexer = new Lexer();

        $this->assertStreamHasTokens(
            [
                [Token::KEYWORD, 'pipeline'],
                [Token::OPENING_CURLY_BRACES],
                [Token::CLOSING_CURLY_BRACES],
            ],
            $lexer->tokenize($pipeline)
        );
    }

    public function testEmptyPipelineWithMultiLineComment()
    {
        $pipeline = <<<PIPE_EOL
pipeline {
    /* insert Declarative Pipeline here */
}
PIPE_EOL;

        $lexer = new Lexer();

        $this->assertStreamHasTokens(
            [
                [Token::KEYWORD, 'pipeline'],
                [Token::OPENING_CURLY_BRACES],
                [Token::MULTIPLE_LINE_COMMENT],
                [Token::CLOSING_CURLY_BRACES],
            ],
            $lexer->tokenize($pipeline)
        );
    }

    public function testEmptyPipelineWithSingleLineComment()
    {
        $pipeline = <<<PIPE_EOL
pipeline {
    // insert Declarative Pipeline here
}
PIPE_EOL;

        $lexer = new Lexer();

        $this->assertStreamHasTokens(
            [
                [Token::KEYWORD, 'pipeline'],
                [Token::OPENING_CURLY_BRACES],
                [Token::SINGLE_LINE_COMMENT],
                [Token::CLOSING_CURLY_BRACES],
            ],
            $lexer->tokenize($pipeline)
        );
    }
}

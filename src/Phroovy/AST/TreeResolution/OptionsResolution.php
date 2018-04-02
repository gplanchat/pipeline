<?php

namespace Kiboko\Component\Phroovy\AST\TreeResolution;

use Kiboko\Component\Phroovy\AST\Exception\UnexpectedTokenException;
use Kiboko\Component\Phroovy\AST\Node;
use Kiboko\Component\Phroovy\AST\TokenConstraint;
use Kiboko\Component\Phroovy\AST\TokenStream;
use Kiboko\Component\Phroovy\Lexer\Token;

class OptionsResolution implements TreeResolutionInterface
{
    /**
     * @var FunctionCallResolution
     */
    private $functionCallResolution;

    /**
     * @param FunctionCallResolution $functionCallResolution
     */
    public function __construct(FunctionCallResolution $functionCallResolution)
    {
        $this->functionCallResolution = $functionCallResolution;
    }

    public function assert(TokenStream $tokenStream): bool
    {
        return $tokenStream->assert(new TokenConstraint(Token::KEYWORD, 'options'));
    }

    public function create(TokenStream $tokenStream): Node\NodeInterface
    {
        $tokenStream->expect(new TokenConstraint(Token::KEYWORD, 'options'));
        $tokenStream->expect(new TokenConstraint(Token::OPENING_CURLY_BRACES));

        $options = [];
        while (!$tokenStream->assert(new TokenConstraint(Token::CLOSING_CURLY_BRACES))) {
            if ($this->functionCallResolution->assert($tokenStream)) {
                $options[] = $this->functionCallResolution->create($tokenStream);
                continue;
            }

            throw UnexpectedTokenException::unmatchedConstraints(
                $tokenStream->watch(),
                [
                    new TokenConstraint(Token::IDENTIFIER),
                ]
            );
        }

        $tokenStream->expect(new TokenConstraint(Token::CLOSING_CURLY_BRACES));

        return new Node\OptionsNode($options);
    }
}

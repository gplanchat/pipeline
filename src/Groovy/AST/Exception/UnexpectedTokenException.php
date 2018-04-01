<?php

namespace Kiboko\Component\Groovy\AST\Exception;

use Kiboko\Component\Groovy\AST\TokenConstraint;
use Kiboko\Component\Groovy\Lexer\Token;

class UnexpectedTokenException extends \RuntimeException
{
    private static function renderConstraintString(TokenConstraint $constraint)
    {
        if ($constraint->value !== null) {
            return sprintf('%s("%s")', $constraint->token, $constraint->value);
        }

        return sprintf('%s(*)', $constraint->token);
    }

    /**
     * @param Token $token
     * @param TokenConstraint $expected
     *
     * @return UnexpectedTokenException
     */
    public static function unmatchedConstraint(Token $token, TokenConstraint $expected): self
    {
        return new self(strtr(
            'Invalid token found line %line%:%column%, found %actual%, was expecting %expected%',
            [
                '%line%' => $token->line,
                '%column%' => $token->column,
                '%expected%' => self::renderConstraintString($expected),
                '%actual%' => sprintf('%s("%s")', $token->token, $token->value),
            ]
        ));
    }

    /**
     * @param Token $token
     * @param iterable|TokenConstraint[] $expectedVariants
     *
     * @return UnexpectedTokenException
     */
    public static function unmatchedConstraints(Token $token, iterable $expectedVariants): self
    {
        return new self(strtr(
            'Invalid token found line %line%:%column%, found %actual%, was expecting one of: %expected%',
            [
                '%line%' => $token->line,
                '%column%' => $token->column,
                '%actual%' => sprintf('%s(%s)', $token->token, $token->value),
                '%expected%' => implode(', ', array_map(function(TokenConstraint $expected) {
                    return self::renderConstraintString($expected);
                }, $expectedVariants))
            ]
        ));
    }

    /**
     * @param Token $token
     *
     * @return UnexpectedTokenException
     */
    public static function expectedString(Token $token): self
    {
        return self::unmatchedConstraints($token, [
                new TokenConstraint(Token::SINGLE_QUOTED_STRING),
                new TokenConstraint(Token::DOUBLE_QUOTED_STRING),
            ]
        );
    }

    /**
     * @param Token $token
     *
     * @return UnexpectedTokenException
     */
    public static function expectedStringOrIdentifier(Token $token): self
    {
        return self::unmatchedConstraints($token, [
                new TokenConstraint(Token::SINGLE_QUOTED_STRING),
                new TokenConstraint(Token::DOUBLE_QUOTED_STRING),
                new TokenConstraint(Token::IDENTIFIER),
            ]
        );
    }
}

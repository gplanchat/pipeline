<?php

namespace Kiboko\Component\Phroovy\AST;

use Kiboko\Component\Phroovy\Lexer\Token;

class TokenConstraint
{
    /**
     * @var string
     */
    public $token;

    /**
     * @var string|null
     */
    public $value;

    /**
     * @param string $token
     * @param null|string $value
     */
    public function __construct(string $token, ?string $value = null)
    {
        $this->token = $token;
        $this->value = $value;
    }

    /**
     * @return self[]|iterable
     */
    public static function anyString(): iterable
    {
        return [
            new self(Token::SINGLE_QUOTED_STRING),
            new self(Token::DOUBLE_QUOTED_STRING),
        ];
    }

    /**
     * @param string|null $name
     *
     * @return TokenConstraint
     */
    public static function identifier(?string $name = null): TokenConstraint
    {
        return new self(Token::IDENTIFIER, $name);
    }

    /**
     * @return self[]|iterable
     */
    public static function anyStringOrIdentifier(): iterable
    {
        return array_merge(
            self::anyString(),
            [
                self::identifier()
            ]
        );
    }

    /**
     * @param string $operator
     *
     * @return TokenConstraint
     */
    public static function operator(string $operator): TokenConstraint
    {
        return new self(Token::OPERATOR, $operator);
    }

    /**
     * @param string[] $operators
     *
     * @return self[]|iterable
     */
    public static function anyOperator(string ...$operators): iterable
    {
        return array_map(function($operator) {
            return self::operator($operator);
        }, $operators);
    }

    /**
     * @param string $keyword
     *
     * @return TokenConstraint
     */
    public static function keyword(string $keyword): TokenConstraint
    {
        return new self(Token::KEYWORD, $keyword);
    }

    /**
     * @param string[] $keywords
     *
     * @return self[]|iterable
     */
    public static function anyKeyword(string ...$keywords): iterable
    {
        return array_map(function($keyword) {
            return self::keyword($keyword);
        }, $keywords);
    }

    /**
     * @return TokenConstraint
     */
    public static function openingCurlyBraces(): TokenConstraint
    {
        return new self(Token::OPENING_CURLY_BRACES);
    }

    /**
     * @return TokenConstraint
     */
    public static function closingCurlyBraces(): TokenConstraint
    {
        return new self(Token::CLOSING_CURLY_BRACES);
    }

    /**
     * @return TokenConstraint
     */
    public static function openingBracket(): TokenConstraint
    {
        return new self(Token::OPENING_BRACKET);
    }

    /**
     * @return TokenConstraint
     */
    public static function closingBracket(): TokenConstraint
    {
        return new self(Token::CLOSING_BRACKET);
    }

    /**
     * @return TokenConstraint
     */
    public static function integer(): TokenConstraint
    {
        return new self(Token::NUMBER_INTEGER);
    }

    /**
     * @return TokenConstraint
     */
    public static function float(): TokenConstraint
    {
        return new self(Token::NUMBER_FLOAT);
    }

    /**
     * @return self[]|iterable
     */
    public static function anyNumber(): iterable
    {
        return [
            new self(Token::NUMBER_FLOAT),
            new self(Token::NUMBER_INTEGER),
        ];
    }
}

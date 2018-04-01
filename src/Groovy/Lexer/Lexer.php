<?php

namespace Kiboko\Component\Groovy\Lexer;

final class Lexer
{
    private static $tokens = [
        Token::KEYWORD => '/\\s*(pipeline|agent|stages|stage|steps|environment|post|always|failure)(\\s*|$)/',
        Token::OPERATOR => '/\\s*(=|,|:)(\\s*|$)/',
        Token::OPENING_CURLY_BRACES => '/\\s*(\\{)(\\s*|$)/',
        Token::CLOSING_CURLY_BRACES => '/\\s*(})(\\s*|$)/',
        Token::OPENING_BRACKET => '/(\\()(\\s*|$)/',
        Token::CLOSING_BRACKET => '/(\\))(\\s*|$)/',
        Token::SINGLE_QUOTED_STRING => '/\\s*\'(([^\'\\\\]|\\\\\\\\|\\\\\'|\\\\)+)\'?(\\s*|$)/',
        Token::DOUBLE_QUOTED_STRING => '/\\s*"(([^"\\\\]|\\\\\\\\|\\\\"|\\\\)+)"?(\\s*|$)/',
        Token::SINGLE_LINE_COMMENT => '/\\s*\\/\\/([^\\n]*)(\\s*|$)/',
        Token::MULTIPLE_LINE_COMMENT => '/\\s*\\/\\*\\s*(.*)\\s*\\*\\/(\\s*|$)/U',
        Token::IDENTIFIER => '/\\s*([^ \\t\\r\\n\\v\\f\\*\\(\\)\\{\\}]+)(\\s*|$)/',
    ];

    /**
     * @param $subject
     *
     * @return Token[]|iterable
     *
     * @throws \Exception
     */
    public function tokenize($subject): iterable
    {
        $length = strlen($subject);
        $cursor = new Cursor();
        while ($cursor->offset < $length) {
            $token = $this->match($subject, $cursor);
            if (null === $token) {
                throw new \Exception(strtr(
                    'Unable to parse subject "%subject%", unexpected input at offset %offset% (line %line%, column %column%)',
                    [
                        '%subject%' => mb_substr($subject, $cursor->offset),
                        '%offset%' => $cursor->offset,
                        '%line%' => $cursor->line,
                        '%column%' => $cursor->column,
                    ]
                ));
            }

            yield $token;
        }
    }

    /**
     * @param string $subject
     * @param Cursor $cursor
     *
     * @return Token|null
     */
    public function match(string $subject, Cursor $cursor): ?Token
    {
        foreach (self::$tokens as $name => $pattern) {
            $matches = [];
            if (1 === preg_match($pattern.'A', $subject, $matches, 0, $cursor->offset)) {
                $token = new Token($name, strlen($matches[0]), $matches[1], $cursor->offset, $cursor->line + 1, $cursor->column);
                $cursor->update($matches[0]);

                return $token;
            }
        }

        return null;
    }
}

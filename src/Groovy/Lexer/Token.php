<?php

namespace Kiboko\Component\Groovy\Lexer;

final class Token
{
    const KEYWORD = 'T_KEYWORD';
    const OPERATOR = 'T_OPERATOR';
    const IDENTIFIER = 'T_IDENTIFIER';
    const OPENING_CURLY_BRACES = 'T_OPENING_CURLY_BRACES';
    const CLOSING_CURLY_BRACES = 'T_CLOSING_CURLY_BRACES';
    const OPENING_BRACKET = 'T_OPENING_BRACKET';
    const CLOSING_BRACKET = 'T_CLOSING_BRACKET';
    const SINGLE_QUOTED_STRING = 'T_SINGLE_QUOTED_STRING';
    const DOUBLE_QUOTED_STRING = 'T_DOUBLE_QUOTED_STRING';
    const SINGLE_LINE_COMMENT = 'T_SINGLE_LINE_COMMENT';
    const MULTIPLE_LINE_COMMENT = 'T_MULTIPLE_LINE_COMMENT';

    /** @var string */
    public $token;

    /** @var int */
    public $size;

    /** @var string */
    public $value;

    /** @var int */
    public $offset;

    /** @var int */
    public $line;

    /** @var int */
    public $column;

    /**
     * @param string $token
     * @param int $size
     * @param string $value
     * @param int $offset
     * @param int $line
     * @param int $column
     */
    public function __construct(string $token, int $size, string $value, int $offset, int $line, int $column)
    {
        $this->token = $token;
        $this->size = $size;
        $this->value = $value;
        $this->offset = $offset;
        $this->line = $line;
        $this->column = $column;
    }
}

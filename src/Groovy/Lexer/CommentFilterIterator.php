<?php

namespace Kiboko\Component\Groovy\Lexer;

class CommentFilterIterator extends \FilterIterator
{
    public function accept()
    {
        /** @var Token $current */
        $current = $this->current();
        if ($current->token === Token::SINGLE_LINE_COMMENT ||
            $current->token === Token::MULTIPLE_LINE_COMMENT
        ) {
            return false;
        }

        return true;
    }
}

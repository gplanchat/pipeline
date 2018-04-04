<?php

namespace Kiboko\Component\Phroovy\Lexer;

class NewLineFilterIterator extends \FilterIterator
{
    /**
     * @var bool
     */
    private $filterNewLines;

    /**
     * @param \Iterator $iterator
     * @param bool $filterNewLines
     */
    public function __construct(\Iterator $iterator, bool $filterNewLines = true)
    {
        parent::__construct($iterator);
        $this->filterNewLines = $filterNewLines;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->filterNewLines;
    }

    public function enable(): void
    {
        $this->filterNewLines = true;
    }

    public function disable(): void
    {
        $this->filterNewLines = false;
    }

    public function accept()
    {
        if ($this->filterNewLines) {
            /** @var Token $current */
            $current = $this->current();
            return $current->token !== Token::NEWLINE;
        }

        return true;
    }
}

<?php

namespace spec\Kiboko\Component\Phroovy\AST;

use Kiboko\Component\Phroovy\AST\TokenStream;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TokenStreamSpec extends ObjectBehavior
{
    function it_is_initializable(
        \Iterator $iterator
    ) {
        $this->beConstructedWith($iterator);
        $this->shouldHaveType(TokenStream::class);
    }
}

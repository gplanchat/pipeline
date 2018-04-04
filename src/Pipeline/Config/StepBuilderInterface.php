<?php

namespace Kiboko\Component\Pipeline\Config;

use Kiboko\Component\Phroovy\AST\Node\StepNode;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;

interface StepBuilderInterface
{
    /**
     * @param StepNode $node
     *
     * @return StepInterface
     */
    public function build(StepNode $node): StepInterface;
}

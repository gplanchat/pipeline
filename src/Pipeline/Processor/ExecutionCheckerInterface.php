<?php

namespace Kiboko\Component\Pipeline\Processor;

use Kiboko\Component\Pipeline\Entity\BuildInterface;
use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;

interface ExecutionCheckerInterface
{
    public function check(
        ExecutionContextInterface $executionContext
    ): bool;
}

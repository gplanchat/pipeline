<?php

namespace Kiboko\Component\Pipeline\Plumbing;

use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;
use Kiboko\Component\Pipeline\ExecutionContext\ProcessManager;
use Kiboko\Component\Pipeline\ExecutionContext\ProcessManagerInterface;

interface StepInterface
{
    /**
     * @param ProcessManagerInterface $processManager
     * @param ExecutionContextInterface $executionContext
     *
     * @return ExecutionContextInterface
     */
    public function __invoke(
        ProcessManagerInterface $processManager,
        ExecutionContextInterface $executionContext
    ): ExecutionContextInterface;
}

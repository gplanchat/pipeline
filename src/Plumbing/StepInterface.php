<?php

namespace Kiboko\Component\Pipeline\Plumbing;

use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;
use Kiboko\Component\Pipeline\ExecutionContext\ProcessHypervisor;
use Kiboko\Component\Pipeline\ExecutionContext\ProcessHypervisorInterface;

interface StepInterface
{
    /**
     * @param ProcessHypervisorInterface $processManager
     * @param ExecutionContextInterface $executionContext
     *
     * @return ExecutionContextInterface
     */
    public function run(
        ProcessHypervisorInterface $processManager,
        ExecutionContextInterface $executionContext
    ): ExecutionContextInterface;
}

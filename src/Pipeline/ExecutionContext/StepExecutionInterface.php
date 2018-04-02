<?php

namespace Kiboko\Component\Pipeline\ExecutionContext;

use Kiboko\Component\Pipeline\ExecutionContext\ExecutionFailure\ExecutionFailureInterface;
use Kiboko\Component\Pipeline\Hypervisor\ProcessHypervisorInterface;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;

interface StepExecutionInterface
{
    /**
     * @param ProcessHypervisorInterface $processManager
     * @param ExecutionContextInterface $executionContext
     *
     * @return ExecutionContextInterface
     */
    public function execute(
        ProcessHypervisorInterface $processManager,
        ExecutionContextInterface $executionContext
    ): ExecutionContextInterface;

    /**
     * @return PipelineExecutionInterface
     */
    public function getPipelineExecution(): PipelineExecutionInterface;

    /**
     * @return StepInterface
     */
    public function getStep(): StepInterface;

    /**
     * @return ExecutionFailureInterface|null
     */
    public function getExecutionFailure(): ?ExecutionFailureInterface;
}

<?php

namespace Kiboko\Component\Pipeline\ExecutionContext;

use Kiboko\Component\Pipeline\ExecutionContext\ExecutionFailure\ExecutionFailureBuilder;
use Kiboko\Component\Pipeline\ExecutionContext\ExecutionFailure\ExecutionFailureInterface;
use Kiboko\Component\Pipeline\Hypervisor\ProcessHypervisorInterface;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;

class StepExecution implements StepExecutionInterface
{
    /**
     * @var PipelineExecutionInterface
     */
    private $pipelineExecution;

    /**
     * @var StepInterface
     */
    private $step;

    /**
     * @var ExecutionFailureBuilder
     */
    private $executionFailureBuilder;

    /**
     * @var ExecutionFailureInterface|null
     */
    private $executionFailure;

    /**
     * StepExecution constructor.
     * @param PipelineExecutionInterface $pipelineExecution
     * @param StepInterface $step
     * @param ExecutionFailureBuilder $executionFailureBuilder
     */
    public function __construct(
        PipelineExecutionInterface $pipelineExecution,
        StepInterface $step,
        ExecutionFailureBuilder $executionFailureBuilder
    ) {
        $this->pipelineExecution = $pipelineExecution;
        $this->step = $step;
        $this->executionFailureBuilder = $executionFailureBuilder;
    }

    /**
     * @param ProcessHypervisorInterface $processManager
     * @param ExecutionContextInterface $executionContext
     *
     * @return ExecutionContextInterface
     */
    public function execute(
        ProcessHypervisorInterface $processManager,
        ExecutionContextInterface $executionContext
    ): ExecutionContextInterface {
        $step = $this->step;
        try {
            return $step->run($processManager, $executionContext);
        } catch (\Throwable $e) {
            $this->executionFailure = $this->executionFailureBuilder->build($e);
        }

        return $executionContext;
    }

    /**
     * @return PipelineExecutionInterface
     */
    public function getPipelineExecution(): PipelineExecutionInterface
    {
        return $this->pipelineExecution;
    }

    /**
     * @return StepInterface
     */
    public function getStep(): StepInterface
    {
        return $this->step;
    }

    public function getExecutionFailure(): ?ExecutionFailureInterface
    {
        return $this->executionFailure;
    }
}

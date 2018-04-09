<?php

namespace Kiboko\Component\Pipeline\Processor;

use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;
use Kiboko\Component\Pipeline\ExecutionContext\ExecutionFailure\ExecutionFailureBuilder;
use Kiboko\Component\Pipeline\ExecutionContext\PipelineExecution;
use Kiboko\Component\Pipeline\ExecutionContext\PipelineExecutionInterface;
use Kiboko\Component\Pipeline\Hypervisor\ProcessHypervisor;
use Kiboko\Component\Pipeline\ExecutionContext\StepExecutionInterface;
use Kiboko\Component\Pipeline\ExecutionContext\InterruptedPipelineExecution;
use Kiboko\Component\Pipeline\Plumbing\StepChainInterface;

class InterruptibleProcessor implements ProcessorInterface
{
    /**
     * @var ProcessHypervisor
     */
    private $processHypervisor;

    /**
     * @var ExecutionFailureBuilder
     */
    private $failureBuilder;

    /**
     * @var ExecutionCheckerInterface
     */
    private $checker;

    /**
     * InterruptibleProcessor constructor.
     *
     * @param ProcessHypervisor $processHypervisor
     * @param ExecutionFailureBuilder $failureBuilder
     * @param ExecutionCheckerInterface $checker
     */
    public function __construct(
        ProcessHypervisor $processHypervisor,
        ExecutionFailureBuilder $failureBuilder,
        ?ExecutionCheckerInterface $checker = null
    ) {
        $this->processHypervisor = $processHypervisor;
        $this->failureBuilder = $failureBuilder;
        $this->checker = $checker;
    }

    /**
     * @param ExecutionContextInterface $executionContext
     * @param StepChainInterface $stepChain
     *
     * @return PipelineExecutionInterface
     */
    public function run(
        ExecutionContextInterface $executionContext,
        StepChainInterface $stepChain
    ): PipelineExecutionInterface {
        $pipelineExecution = new PipelineExecution($stepChain, $this->failureBuilder);
        return $this->execute($pipelineExecution, $executionContext);
    }

    /**
     * @param PipelineExecutionInterface $pipelineExecution
     * @param ExecutionContextInterface $executionContext
     *
     * @return PipelineExecutionInterface
     */
    private function execute(
        PipelineExecutionInterface $pipelineExecution,
        ExecutionContextInterface $executionContext
    ): PipelineExecutionInterface {
        /** @var StepExecutionInterface $stepExecution */
        foreach ($pipelineExecution as $stepExecution) {
            $executionContext = $stepExecution->execute(
                $this->processHypervisor, $executionContext
            );

            if ($this->checker !== null &&
                true !== $this->checker->check($executionContext)
            ) {
                return new InterruptedPipelineExecution($pipelineExecution, $executionContext);
            }
        }

        return $pipelineExecution;
    }

    /**
     * @param InterruptedPipelineExecution $pipelineExecution
     *
     * @return PipelineExecutionInterface
     */
    public function resume(InterruptedPipelineExecution $pipelineExecution): PipelineExecutionInterface
    {
        return $this->execute($pipelineExecution, $pipelineExecution->getExecutionContext());
    }
}

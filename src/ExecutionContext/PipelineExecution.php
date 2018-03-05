<?php

namespace Kiboko\Component\Pipeline\ExecutionContext;

use Kiboko\Component\Pipeline\ExecutionContext\ExecutionFailure\ExecutionFailureBuilder;
use Kiboko\Component\Pipeline\Plumbing\StepChain;
use Kiboko\Component\Pipeline\Plumbing\StepChainInterface;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;

class PipelineExecution implements PipelineExecutionInterface
{
    /**
     * @var StepChainInterface[]
     */
    private $stepChain;

    /**
     * @var StepExecutionInterface[]
     */
    private $stepExecutions;

    /**
     * @var ExecutionFailureBuilder
     */
    private $executionFailureBuilder;

    /**
     * @param StepChainInterface $stepChain
     * @param ExecutionFailureBuilder $executionFailureBuilder
     */
    public function __construct(
        StepChainInterface $stepChain,
        ExecutionFailureBuilder $executionFailureBuilder
    ) {
        $this->stepChain = $stepChain;
        $this->executionFailureBuilder = $executionFailureBuilder;
        $this->stepExecutions = new \ArrayIterator();
    }

    public function getIterator()
    {
        foreach ($this->stepChain->getIterator() as $step) {
            $execution = new StepExecution($this, $step, $this->executionFailureBuilder);
            $this->stepExecutions->append($execution);

            yield $execution;
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->stepExecutions->count();
    }
}

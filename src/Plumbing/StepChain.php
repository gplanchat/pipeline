<?php

namespace Kiboko\Component\Pipeline\Plumbing;

use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;
use Kiboko\Component\Pipeline\ExecutionContext\ProcessManagerInterface;

class StepChain implements StepChainInterface
{
    /**
     * @var StepInterface[]
     */
    private $steps;

    /**
     * @param StepInterface[] $steps
     */
    public function __construct(StepInterface ...$steps)
    {
        $this->steps = $steps;
    }

    /**
     * @param StepInterface[] ...$steps
     *
     * @return StepChainInterface
     */
    public function pipe(StepInterface ...$steps): StepChainInterface
    {
        array_push($this->steps, ...$steps);

        return $this;
    }

    /**
     * @param ProcessManagerInterface $processManager
     * @param ExecutionContextInterface $executionContext
     *
     * @return ExecutionContextInterface
     */
    public function __invoke(
        ProcessManagerInterface $processManager,
        ExecutionContextInterface $executionContext
    ): ExecutionContextInterface {
        foreach ($this->steps as $step) {
            $executionContext = $step($processManager, $executionContext);
        }

        return $executionContext;
    }

    public function getIterator()
    {
        yield from $this->steps;
    }

    public function count()
    {
        return count($this->steps);
    }
}

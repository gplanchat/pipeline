<?php

namespace Kiboko\Component\Pipeline\Plumbing;

use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;
use Kiboko\Component\Pipeline\Hypervisor\ProcessHypervisorInterface;

class StepGroup implements StepGroupInterface
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
     * @return StepGroupInterface
     */
    public function fork(StepInterface ...$steps): StepGroupInterface
    {
        array_push($this->steps, ...$steps);

        return $this;
    }

    /**
     * @param ProcessHypervisorInterface $processManager
     * @param ExecutionContextInterface $executionContext
     *
     * @return ExecutionContextInterface
     */
    public function run(
        ProcessHypervisorInterface $processManager,
        ExecutionContextInterface $executionContext
    ): ExecutionContextInterface {
        foreach ($this->steps as $step) {
            $step->run($processManager, $executionContext);
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

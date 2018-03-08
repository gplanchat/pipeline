<?php

namespace Project\Step;

use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;
use Kiboko\Component\Pipeline\ExecutionContext\ProcessHypervisorInterface;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;

class BazStep implements StepInterface
{
    private $param;

    /**
     * @param string $param
     */
    public function __construct(string $param)
    {
        $this->param = $param;
    }

    public function run(
        ProcessHypervisorInterface $processManager,
        ExecutionContextInterface $executionContext
    ): ExecutionContextInterface {
        echo 'STARTING:' . __METHOD__ . '("' . $this->param . '")' . PHP_EOL;

        sleep(3);

        echo 'ENDING:' . __METHOD__ . '("' . $this->param . '")' . PHP_EOL;

        return $executionContext;
    }
}

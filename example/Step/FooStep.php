<?php

namespace Project\Step;

use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;
use Kiboko\Component\Pipeline\Hypervisor\ProcessHypervisorInterface;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;
use Kiboko\Component\Pipeline\Step\ThenableStepTrait;

class FooStep implements StepInterface
{
    use ThenableStepTrait;

    public function run(
        ProcessHypervisorInterface $processHypervisor,
        ExecutionContextInterface $executionContext
    ): ExecutionContextInterface {
        echo 'STARTING:' . __METHOD__ . '()' . PHP_EOL;

        sleep(3);

        echo 'ENDING:' . __METHOD__ . '()' . PHP_EOL;

        return $executionContext;
    }
}

<?php

namespace Project\Step;

use Kiboko\Component\Pipeline\ExecutionContext\ExecutionContextInterface;
use Kiboko\Component\Pipeline\ExecutionContext\ProcessHypervisorInterface;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;

class BarStep implements StepInterface
{
    public function run(
        ProcessHypervisorInterface $processManager,
        ExecutionContextInterface $executionContext
    ): ExecutionContextInterface {
        echo 'STARTING:' . __METHOD__ . '()' . PHP_EOL;

        sleep(3);

        echo 'ENDING:' . __METHOD__ . '()' . PHP_EOL;

        return $executionContext;
    }
}
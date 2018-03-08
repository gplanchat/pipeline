<?php

namespace Kiboko\Component\Pipeline\ExecutionContext;

use Symfony\Component\Process\Process;

interface ProcessHypervisorInterface extends \Countable
{
    /**
     * @param Process $process
     * @param callable|null $callback
     *
     * @return ProcessHypervisorInterface
     */
    public function enqueue(Process $process, ?callable $callback = null): ProcessHypervisorInterface;

    /**
     * @param callable $loopController
     *
     * @return ProcessHypervisorInterface
     */
    public function run(callable $loopController): ProcessHypervisorInterface;
}

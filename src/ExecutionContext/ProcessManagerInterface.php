<?php

namespace Kiboko\Component\Pipeline\ExecutionContext;

use Symfony\Component\Process\Process;

interface ProcessManagerInterface extends \Countable
{
    /**
     * @param Process $process
     * @param callable|null $callback
     *
     * @return ProcessManagerInterface
     */
    public function enqueue(Process $process, ?callable $callback = null): ProcessManagerInterface;

    /**
     * @param callable $loopController
     *
     * @return ProcessManagerInterface
     */
    public function run(callable $loopController): ProcessManagerInterface;
}

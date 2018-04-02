<?php

namespace Kiboko\Component\Pipeline\Hypervisor;

use React\ChildProcess\Process;
use React\Promise\ExtendedPromiseInterface;

class ProcessBatch implements ProcessBatchInterface
{
    /**
     * @var Process[]|\SplObjectStorage
     */
    private $processes;

    public function __construct()
    {
        $this->processes = new \SplObjectStorage();
    }

    public function enqueue(Process $process, callable $callback = null): ExtendedPromiseInterface
    {
        $this->processes->attach($process, $callback);
    }

    public function run(ProcessHypervisorInterface $hypervisor): void
    {
        foreach ($this->processes as $process) {
            $hypervisor->enqueue($process, $this->processes->offsetGet($process));
        }
    }

    public function getIterator()
    {
        yield from $this->processes;
    }

    public function count()
    {
        return count($this->processes);
    }
}

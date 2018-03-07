<?php

namespace Kiboko\Component\Pipeline\ExecutionContext;

use Symfony\Component\Process\Process;

class ProcessManager implements ProcessManagerInterface
{
    /**
     * @var int
     */
    private $maxProcesses;

    /**
     * @var int
     */
    private $pollTime;

    /**
     * @var Process[]
     */
    private $pendingProcesses;

    /**
     * @var Process[]
     */
    private $currentProcesses;

    /**
     * ProcessManager constructor.
     *
     * @param int $maxProcesses
     * @param int $pollTime
     */
    public function __construct($maxProcesses = 1, $pollTime = 1000)
    {
        $this->maxProcesses = max($maxProcesses, 1);
        $this->pollTime = $pollTime;
        $this->pendingProcesses = new \SplObjectStorage();
        $this->currentProcesses = new \SplObjectStorage();
    }

    /**
     * @param Process $process
     * @param callable|null $callback
     *
     * @return ProcessManagerInterface
     */
    public function enqueue(
        Process $process,
        ?callable $callback = null
    ): ProcessManagerInterface {
        $this->pendingProcesses->attach($process, $callback);

        return $this;
    }

    private function pickFromPendingProcesses(int $processesToStart): \SplObjectStorage
    {
        $newProcesses = new \SplObjectStorage();
        foreach ($this->pendingProcesses as $process) {
            $callback = $this->pendingProcesses->offsetGet($process);
            $newProcesses->attach($process, $callback);

            if (--$processesToStart <= 0) {
                break;
            }
        }

        $this->pendingProcesses->removeAll($newProcesses);

        return $newProcesses;
    }

    private function probeAndPickStoppedProcesses(): \SplObjectStorage
    {
        $stoppedProcesses = new \SplObjectStorage();
        foreach ($this->currentProcesses as $process) {
            if ($process->isRunning()) {
                continue;
            }

            $callback = $this->currentProcesses->offsetGet($process);
            $stoppedProcesses->attach($process, $callback);

            if ($callback !== null) {
                $callback($this, $process);
            }
        }

        $this->currentProcesses->removeAll($stoppedProcesses);

        return $stoppedProcesses;
    }

    public function run(callable $loopController): ProcessManagerInterface
    {
        while (true) {
            $processesToStart = $this->maxProcesses - $this->currentProcesses->count();
            if ($processesToStart > 0) {
                $newProcesses = $this->pickFromPendingProcesses($processesToStart);
                $this->startProcesses($newProcesses);

                $this->currentProcesses->addAll($newProcesses);
            }

            usleep($this->pollTime);

            $stoppedProcesses = $this->probeAndPickStoppedProcesses();

            if ($loopController($this, iterator_to_array($stoppedProcesses), $this->count()) === false) {
                foreach ($this->currentProcesses as $process) {
                    $process->stop(10, SIGSTOP);
                }
                break;
            }

            if ($this->count() <= 0) {
                break;
            }
        }

        return $this;
    }

    /**
     * @param Process[]|\Traversable $processes
     */
    private function startProcesses(\Traversable $processes)
    {
        foreach ($processes as $process) {
            $process->start();
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->pendingProcesses) + count($this->currentProcesses);
    }
}

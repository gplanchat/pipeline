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

    public function run(callable $loopController): ProcessManagerInterface
    {
        do {
            if ($this->count() <= 0) {
                break;
            }

            $processesToStart = $this->maxProcesses - $this->currentProcesses->count();
            if ($processesToStart > 0) {
                $newProcesses = new \SplObjectStorage();
                foreach ($this->pendingProcesses as $process) {
                    $callback = $this->pendingProcesses->offsetGet($process);
                    $this->pendingProcesses->detach($process);

                    $newProcesses->attach($process, $callback);

                    if (--$processesToStart <= 0) {
                        break;
                    }
                }
                $this->startProcesses($newProcesses);

                $this->currentProcesses->addAll($newProcesses);
            }

            usleep($this->pollTime);

            $stoppedProcesses = [];
            foreach ($this->currentProcesses as $process) {
                if ($process->isRunning()) {
                    continue;
                }

                $callback = $this->currentProcesses->offsetGet($process);
                $this->currentProcesses->detach($process);

                if ($callback !== null) {
                    $callback($this, $process);
                }

                $stoppedProcesses[] = $process;
            }
        } while ($loopController($this, $stoppedProcesses));

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

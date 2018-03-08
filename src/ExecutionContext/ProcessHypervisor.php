<?php

namespace Kiboko\Component\Pipeline\ExecutionContext;

use Symfony\Component\Process\Process;

class ProcessHypervisor implements ProcessHypervisorInterface
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

    public function __destruct()
    {
        $this->stopProcesses($this->currentProcesses, 10, SIGSTOP);
    }

    /**
     * @param Process $process
     * @param callable|null $callback
     *
     * @return ProcessHypervisorInterface
     */
    public function enqueue(
        Process $process,
        ?callable $callback = null
    ): ProcessHypervisorInterface {
        $this->pendingProcesses->attach($process, $callback);

        return $this;
    }

    /**
     * @param int $processesToStart
     *
     * @return \SplObjectStorage
     */
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

    /**
     * @return \SplObjectStorage
     */
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

    public function run(callable $loopController): ProcessHypervisorInterface
    {
        while ($this->count() > 0) {
            $processesToStart = $this->maxProcesses - $this->currentProcesses->count();
            if ($processesToStart > 0) {
                $newProcesses = $this->pickFromPendingProcesses($processesToStart);
                $this->startProcesses($newProcesses);

                $this->currentProcesses->addAll($newProcesses);
            }

            $stoppedProcesses = $this->probeAndPickStoppedProcesses();

            if ($loopController($this, iterator_to_array($stoppedProcesses), $this->count()) === false) {
                $this->stopProcesses($this->currentProcesses, 10, SIGSTOP);
                break;
            }

            usleep($this->pollTime);
        }

        return $this;
    }

    /**
     * @param Process[]|iterable $processes
     */
    private function startProcesses(iterable $processes)
    {
        foreach ($processes as $process) {
            $process->start();
        }
    }

    /**
     * @param Process[]|iterable $processes
     * @param int $timeout
     * @param int $signal
     */
    private function stopProcesses(?iterable $processes, int $timeout = 10, int $signal = SIGTERM)
    {
        if ($processes === null) {
            return;
        }

        foreach ($processes as $process) {
            $process->stop($timeout, $signal);
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->pendingProcesses->count() + $this->currentProcesses->count();
    }
}

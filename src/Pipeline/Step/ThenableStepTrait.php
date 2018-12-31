<?php

namespace Kiboko\Component\Pipeline\Step;

use Kiboko\Component\Pipeline\Plumbing\StepInterface;
use React\ChildProcess\Process;

trait ThenableStepTrait
{
    /**
     * @var callable[]
     */
    private $thenCallbacks = [];

    /**
     * @var callable[]
     */
    private $otherwiseCallbacks = [];

    public function then(callable $callback): StepInterface
    {
        $this->thenCallbacks[] = $callback;

        return $this;
    }

    public function otherwise(callable $callback): StepInterface
    {
        $this->otherwiseCallbacks[] = $callback;

        return $this;
    }

    private function registerProcess(Process $process)
    {
        $process->on('exit', function($exitCode, $termSignal) {
            if ($exitCode === 0) {
                foreach ($this->thenCallbacks as $callback) {
                    $callback($this->resultFiles);
                }
            }

            if ($exitCode === 0) {
                foreach ($this->otherwiseCallbacks as $callback) {
                    $callback($this->resultFiles, $exitCode, $termSignal);
                }
            }
        });
    }
}

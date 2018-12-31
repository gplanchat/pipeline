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

    public function then(callable ...$callbacks): StepInterface
    {
        $this->thenCallbacks = array_merge(
            $this->thenCallbacks,
            $callbacks
        );

        return $this;
    }

    public function otherwise(callable ...$callbacks): StepInterface
    {
        $this->otherwiseCallbacks = array_merge(
            $this->otherwiseCallbacks,
            $callbacks
        );

        return $this;
    }

    public function always(callable ...$callbacks): StepInterface
    {
        $this->thenCallbacks = array_merge(
            $this->thenCallbacks,
            $callbacks
        );

        $this->otherwiseCallbacks = array_merge(
            $this->otherwiseCallbacks,
            $callbacks
        );

        return $this;
    }

    private function registerProcess(Process $process)
    {
        $process->on('exit', function($exitCode, $termSignal) {
            if ($exitCode === 0) {
                foreach ($this->thenCallbacks as $callback) {
                    $callback($this);
                }

                return;
            }

            foreach ($this->otherwiseCallbacks as $callback) {
                $callback($this, $exitCode, $termSignal);
            }
        });
    }
}

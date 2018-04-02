<?php

namespace Kiboko\Component\Pipeline\Plumbing;

interface StepGroupInterface extends StepInterface, \IteratorAggregate, \Countable
{
    /**
     * @param StepInterface[] ...$steps
     *
     * @return StepGroupInterface
     */
    public function fork(StepInterface ...$steps): StepGroupInterface;
}

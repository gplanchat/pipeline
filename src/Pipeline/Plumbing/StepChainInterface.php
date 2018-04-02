<?php

namespace Kiboko\Component\Pipeline\Plumbing;

interface StepChainInterface extends StepInterface, \IteratorAggregate, \Countable
{
    /**
     * @param StepInterface[] ...$steps
     *
     * @return StepChainInterface
     */
    public function pipe(StepInterface ...$steps): StepChainInterface;
}

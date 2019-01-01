<?php

namespace spec\Kiboko\Component\Pipeline\ExecutionContext;

use Kiboko\Component\Pipeline\ExecutionContext\ExecutionFailure\ExecutionFailureBuilder;
use Kiboko\Component\Pipeline\ExecutionContext\PipelineExecution;
use Kiboko\Component\Pipeline\ExecutionContext\StepExecutionInterface;
use Kiboko\Component\Pipeline\Plumbing\StepChainInterface;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;
use PhpSpec\ObjectBehavior;

class PipelineExecutionSpec extends ObjectBehavior
{
    function it_is_initializable(
        StepChainInterface $stepChain,
        ExecutionFailureBuilder $failureBuilder
    ) {
        $this->beConstructedWith($stepChain, $failureBuilder);
        $this->shouldHaveType(PipelineExecution::class);
    }

    function it_can_iterate_steps(
        StepChainInterface $stepChain,
        ExecutionFailureBuilder $failureBuilder,
        StepInterface $step,
        StepExecutionInterface $stepExecution
    ) {
        $this->beConstructedWith($stepChain, $failureBuilder);
        $this->shouldHaveType(\IteratorAggregate::class);

        $stepChain->getIterator()
            ->willReturn(new \ArrayIterator([
                $step->getWrappedObject()
            ]));

        $this->getIterator()
            ->shouldReturnAnInstanceOf(\Iterator::class);
        $this->getIterator()
            ->shouldIterateAsInstancesOf(StepExecutionInterface::class);
    }

    public function getMatchers(): array
    {
        return [
            'iterateAsInstancesOf' => function ($subject, $key) {
                foreach ($subject as $item) {
                    if (!is_a($item, $key)) {
                        return false;
                    }
                }

                return true;
            },
        ];
    }
}

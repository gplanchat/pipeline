<?php

namespace Kiboko\Component\Pipeline\ExecutionContext;

class ConfigInterpreter
{
    public function interpret(array $config)
    {
        $pipeline = new Kiboko\Component\Pipeline\Plumbing\Pipeline(
            new Kiboko\Component\Pipeline\Plumbing\StepChain(
                new Project\Step\FooStep(),
                new Project\Step\BarStep(),
                new Kiboko\Component\Pipeline\Plumbing\StepGroup(
                    new Project\Step\BazStep('A'),
                    new Kiboko\Component\Pipeline\Plumbing\StepChain(
                        new Project\Step\BazStep('B'),
                        new Project\Step\BazStep('C')
                    ),
                    new Project\Step\BazStep('D')
                )
            )
        );
    }

    public function read(array $config)
    {

    }
}

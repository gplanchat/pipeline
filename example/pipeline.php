<?php

require __DIR__ . '/../vendor/autoload.php';

require 'Step/FooStep.php';
require 'Step/BarStep.php';
require 'Step/BazStep.php';

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

$runner = new \Kiboko\Component\Pipeline\PipelineRunner(
    new \Kiboko\Component\Pipeline\Processor\InterruptibleProcessor(
        new \Kiboko\Component\Pipeline\ExecutionContext\ProcessHypervisor(5),
        new \Kiboko\Component\Pipeline\ExecutionContext\ExecutionFailure\ExecutionFailureBuilder()
    )
);

$runner->run($pipeline, new \Kiboko\Component\Pipeline\ExecutionContext\ShellExecutionContext());

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
        new \Kiboko\Component\Pipeline\Hypervisor\ProcessHypervisor(5),
        new \Kiboko\Component\Pipeline\ExecutionContext\ExecutionFailure\ExecutionFailureBuilder()
    )
);

//$runner->run($pipeline, new \Kiboko\Component\Pipeline\ExecutionContext\ShellExecutionContext());

$lexer = new \Kiboko\Component\Jenkinsfile\Lexer\Lexer();

//var_dump(iterator_to_array($lexer->tokenize(file_get_contents(__DIR__ . '/Jenkinsfile'))));

$ast = new \Kiboko\Component\Jenkinsfile\AST\Tree();

var_dump($ast->compile($lexer->tokenize(file_get_contents(__DIR__ . '/Jenkinsfile'))));

$config = new \Kiboko\Component\Pipeline\Config\Config(new \Kiboko\Component\Pipeline\Config\DemoStepBuilder());

//var_dump(iterator_to_array($config->compile($ast->compile($lexer->tokenize(file_get_contents(__DIR__ . '/Jenkinsfile'))))));

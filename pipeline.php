<?php

require __DIR__ . '/vendor/autoload.php';

use Kiboko\Component\Phroovy\AST\Tree;
use Kiboko\Component\Phroovy\Lexer\Lexer;
use Kiboko\Component\Pipeline\Config\Config;
use Kiboko\Component\Pipeline\Config\DemoStepBuilder;
use Kiboko\Component\Pipeline\Hypervisor\ProcessHypervisor;
use Kiboko\Component\Pipeline\ExecutionContext\ShellExecutionContext;
use Kiboko\Component\Pipeline\Processor\InterruptibleProcessor;
use Kiboko\Component\Pipeline\ExecutionContext\ExecutionFailure\ExecutionFailureBuilder;

$lexer = new Lexer();
$tree = new Tree();
$config = new Config(new DemoStepBuilder());

$tokenStream = $lexer->tokenize(file_get_contents(__DIR__ . '/Kibokofile'));

$nodeStream = $tree->compile($tokenStream);

$loop = \React\EventLoop\Factory::create();

$hypervisor = new ProcessHypervisor();

$executionContext = new ShellExecutionContext();
$processor = new InterruptibleProcessor(
    $hypervisor,
    new ExecutionFailureBuilder()
);

foreach ($config->compile($nodeStream) as $pipeline) {
    $pipeline->run($executionContext, $processor);
}

$hypervisor->run($loop);

$loop->run();

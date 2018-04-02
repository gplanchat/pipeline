<?php

namespace Kiboko\Component\Pipeline\Config;

use Kiboko\Component\Jenkinsfile\AST\StepNode;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;
use Kiboko\Component\Pipeline\Step\CommandStep;

class DemoStepBuilder implements StepBuilderInterface
{
    /**
     * @param StepNode $node
     *
     * @return StepInterface
     */
    public function build(StepNode $node): StepInterface
    {
        if ($node->type === 'sh') {
            return new CommandStep($node->arguments);
        }
        if ($node->type === 'junit') {
            return new CommandStep(array_merge(['junit'], $node->arguments));
        }
        if ($node->type === 'phpunit') {
            return new CommandStep(array_merge(['phpunit'], $node->arguments));
        }

        throw new \RuntimeException('Unknown step type.');
    }
}

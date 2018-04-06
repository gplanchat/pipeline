<?php

namespace Kiboko\Component\Pipeline\Config;

use Kiboko\Component\Phroovy\AST\Node\StepNode;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;
use Kiboko\Component\Pipeline\Step\CommandStep;
use Kiboko\Component\Pipeline\Step\CopyStep;
use Kiboko\Component\Pipeline\Step\PHPSpecStep;
use Kiboko\Component\Pipeline\Step\PHPUnitStep;

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
            return CommandStep::fromConfig($node->arguments->toPHPValue());
        }
        if ($node->type === 'copy') {
            return CopyStep::fromConfig($node->arguments->toPHPValue());
        }
        if ($node->type === 'phpunit') {
            return PHPUnitStep::fromConfig($node->arguments->toPHPValue());
        }
        if ($node->type === 'phpspec') {
            return PHPSpecStep::fromConfig($node->arguments->toPHPValue());
        }

        throw new \RuntimeException(strtr(
            'Unknown step type: %type%.',
            [
                '%type%' => $node->type,
            ]
        ));
    }
}

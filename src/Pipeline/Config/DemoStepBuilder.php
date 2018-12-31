<?php

namespace Kiboko\Component\Pipeline\Config;

use Kiboko\Component\JUnitXMLFile\JUnitReader;
use Kiboko\Component\JUnitXMLFile\Result\JUnitSuite;
use Kiboko\Component\Phroovy\AST\Node\StepNode;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;
use Kiboko\Component\Pipeline\Step\CommandStep;
use Kiboko\Component\Pipeline\Step\CopyStep;
use Kiboko\Component\PHPSpecPipeline\Step\PHPSpecStep;
use Kiboko\Component\PHPUnitPipeline\Step\PHPUnitStep;

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
            $step = PHPUnitStep::fromConfig($node->arguments->toPHPValue());

            $step->always(function(PHPUnitStep $step) {
                foreach ($step->getResultFiles() as $file) {
                    $suiteIterator = JUnitReader::fromFile($file);
                    /** @var JUnitSuite $suite */
                    foreach ($suiteIterator as $suite) {
                        printSuite($suite);
                    }
                    break;
                }
            });

            return $step;
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

function prettyPrint(?string $label, int $level = 0)
{
    $color = sprintf('1;%d', 30 + $level);
    $pad = str_pad('', 2 * $level, ' ');

    echo "$pad\033[${color}m - " . ($label ?? '<Anonymous>') . "\033[0m" . PHP_EOL;
}

function secondaryPrint(?string $label, $level = 0)
{
    $color = sprintf('0;%d', 90 + $level);
    $pad = str_pad('', 2 + 2 * $level, ' ');

    echo "$pad * \033[${color}m" . ($label ?? '<Anonymous>') . "\033[0m" . PHP_EOL;
}

function printSuite(JUnitSuite $suite, $level = 0)
{
    prettyPrint($suite->name, $level);

    foreach ($suite as $case) {
        secondaryPrint($case->name, $level);
    }

    foreach ($suite->getChildren() as $childSuite) {
        printSuite($childSuite, $level + 1);
    }
}

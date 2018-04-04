<?php

namespace Kiboko\Component\Pipeline\Config;

use Kiboko\Component\Phroovy\AST\Node\NodeInterface;
use Kiboko\Component\Phroovy\AST\Node\PipelineNode;
use Kiboko\Component\Phroovy\AST\Node\StageCollectionNode;
use Kiboko\Component\Phroovy\AST\Node\StageNode;
use Kiboko\Component\Phroovy\AST\Node\StepCollectionNode;
use Kiboko\Component\Pipeline\Plumbing\Pipeline;
use Kiboko\Component\Pipeline\Plumbing\PipelineInterface;
use Kiboko\Component\Pipeline\Plumbing\StepChain;
use Kiboko\Component\Pipeline\Plumbing\StepInterface;

class Config
{
    /**
     * @var StepBuilderInterface
     */
    private $stepBuilder;

    /**
     * @param StepBuilderInterface $stepBuilder
     */
    public function __construct(StepBuilderInterface $stepBuilder)
    {
        $this->stepBuilder = $stepBuilder;
    }

    /**
     * @param NodeInterface[]|iterable $nodeTrees
     *
     * @return PipelineInterface[]|iterable
     */
    public function compile(iterable $nodeTrees): iterable
    {
        if (is_array($nodeTrees)) {
            $nodeTrees = new \ArrayIterator($nodeTrees);
        }

        if ($nodeTrees instanceof \IteratorAggregate) {
            $nodeTrees = $nodeTrees->getIterator();
        }

        foreach ($nodeTrees as $tree) {
            yield $this->createPipeline($tree);
        }
    }

    /**
     * @param PipelineNode $node
     *
     * @return PipelineInterface
     */
    private function createPipeline(PipelineNode $node): PipelineInterface
    {
        return new Pipeline(
            new StepChain(
                ...$this->createStages($node->stages)
            )
        );
    }

    /**
     * @param StageCollectionNode $node
     *
     * @return StepInterface[]|iterable
     */
    private function createStages(StageCollectionNode $node): iterable
    {
        /** @var StageNode $stage */
        foreach ($node as $stage) {
            yield new StepChain(
                ...$this->createSteps($stage->steps)
            );
        }
    }

    /**
     * @param StepCollectionNode $node
     *
     * @return StepInterface[]|iterable
     */
    private function createSteps(StepCollectionNode $node): iterable
    {
        foreach ($node as $step) {
            yield $this->stepBuilder->build($step);
        }
    }
}

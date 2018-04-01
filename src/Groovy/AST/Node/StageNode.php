<?php

namespace Kiboko\Component\Groovy\AST\Node;

class StageNode implements NodeInterface
{
    use EnvironmentAwareNode;
    use PostActionsAwareNode;

    /**
     * @var string
     */
    public $label;

    /**
     * @var StepCollectionNode
     */
    public $steps;

    /**
     * @param string $label
     * @param StepCollectionNode $steps
     */
    public function __construct(?string $label = null, StepCollectionNode $steps = null)
    {
        $this->label = $label;
        $this->steps = $steps ?? new StepCollectionNode();
    }
}

<?php

namespace Kiboko\Component\Phroovy\AST\Node;

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
     * @var AgentNode
     */
    public $agent;

    /**
     * @param string $label
     * @param StepCollectionNode $steps
     * @param AgentNode $agent
     * @param EnvironmentNode|null $environment
     */
    public function __construct(
        ?string $label = null,
        StepCollectionNode $steps = null,
        AgentNode $agent = null,
        ?EnvironmentNode $environment = null
    ) {
        $this->label = $label;
        $this->steps = $steps ?? new StepCollectionNode();
        $this->agent = $agent;
        $this->environment = $environment;
    }
}

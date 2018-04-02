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
     * @var Agent\AgentNode
     */
    public $agent;

    /**
     * @var OptionsNode
     */
    public $options;

    /**
     * @param string $label
     * @param StepCollectionNode $steps
     * @param Agent\AgentNode $agent
     * @param EnvironmentNode|null $environment
     */
    public function __construct(
        ?string $label = null,
        StepCollectionNode $steps = null,
        Agent\AgentNode $agent = null,
        ?EnvironmentNode $environment = null
    ) {
        $this->label = $label;
        $this->steps = $steps ?? new StepCollectionNode();
        $this->agent = $agent;
        $this->environment = $environment;
    }
}

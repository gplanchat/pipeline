<?php

namespace Kiboko\Component\Phroovy\AST\Node;

class StageNode implements NodeInterface
{
    use EnvironmentAwareNode;
    use PostActionsAwareNode;
    use AgentAwareNode;

    /**
     * @var string
     */
    public $label;

    /**
     * @var StepCollectionNode
     */
    public $steps;

    /**
     * @var OptionsNode
     */
    public $options;

    /**
     * @param string|null $label
     * @param StepCollectionNode|null $steps
     * @param Agent\AgentNodeInterface|null $agent
     * @param EnvironmentNode|null $environment
     */
    public function __construct(
        ?string $label = null,
        ?StepCollectionNode $steps = null,
        ?Agent\AgentNodeInterface $agent = null,
        ?EnvironmentNode $environment = null
    ) {
        $this->label = $label;
        $this->steps = $steps ?? new StepCollectionNode();
        $this->agent = $agent;
        $this->environment = $environment;
    }
}

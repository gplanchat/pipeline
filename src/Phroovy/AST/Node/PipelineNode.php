<?php

namespace Kiboko\Component\Phroovy\AST\Node;

class PipelineNode implements NodeInterface
{
    use AgentAwareNode;
    use EnvironmentAwareNode;
    use PostActionsAwareNode;

    /**
     * @var StageCollectionNode
     */
    public $stages;

    /**
     * @var OptionsNode
     */
    public $options;

    /**
     * @param StageCollectionNode $stages
     * @param Agent\AgentNodeInterface|null $agent
     * @param EnvironmentNode|null $environment
     * @param PostActionNode|null $post
     * @param OptionsNode|null $options
     */
    public function __construct(
        StageCollectionNode $stages = null,
        ?Agent\AgentNodeInterface $agent = null,
        ?EnvironmentNode $environment = null,
        ?PostActionNode $post = null,
        ?OptionsNode $options = null
    ) {
        $this->stages = $stages ?? new StageCollectionNode();
        $this->agent = $agent;
        $this->environment = $environment;
        $this->post = $post;
        $this->options = $options;
    }
}

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
     * @param StageCollectionNode $stages
     * @param AgentNode|null $agent
     * @param EnvironmentNode|null $environment
     * @param PostActionNode|null $post
     */
    public function __construct(
        StageCollectionNode $stages = null,
        ?AgentNode $agent = null,
        ?EnvironmentNode $environment = null,
        ?PostActionNode $post = null
    ) {
        $this->stages = $stages ?? new StageCollectionNode();
        $this->agent = $agent;
        $this->environment = $environment;
        $this->post = $post;
    }
}

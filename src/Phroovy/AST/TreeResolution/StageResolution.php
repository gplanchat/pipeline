<?php

namespace Kiboko\Component\Phroovy\AST\TreeResolution;

use Kiboko\Component\Phroovy\AST\Exception;
use Kiboko\Component\Phroovy\AST\Node;
use Kiboko\Component\Phroovy\AST\TokenConstraint;
use Kiboko\Component\Phroovy\AST\TokenStream;
use Kiboko\Component\Phroovy\Lexer\Token;

class StageResolution implements TreeResolutionInterface
{
    /**
     * @var StepCollectionResolution
     */
    private $stepCollectionResolution;

    /**
     * @var EnvironmentResolution
     */
    private $environmentResolution;

    /**
     * @var AgentResolution
     */
    private $agentResolution;

    /**
     * @var PostActionResolution
     */
    private $postActionResolution;

    /**
     * @var OptionsResolution
     */
    private $optionsResolution;

    /**
     * @param StepCollectionResolution $stepCollectionResolution
     * @param EnvironmentResolution $environmentResolution
     * @param AgentResolution $agentResolution
     * @param PostActionResolution $postActionResolution
     * @param OptionsResolution $optionsResolution
     */
    public function __construct(
        StepCollectionResolution $stepCollectionResolution,
        EnvironmentResolution $environmentResolution,
        AgentResolution $agentResolution,
        PostActionResolution $postActionResolution,
        OptionsResolution $optionsResolution
    ) {
        $this->stepCollectionResolution = $stepCollectionResolution;
        $this->environmentResolution = $environmentResolution;
        $this->agentResolution = $agentResolution;
        $this->postActionResolution = $postActionResolution;
        $this->optionsResolution = $optionsResolution;
    }

    /**
     * @return Token[]|iterable
     */
    public function constraints(): iterable
    {
        return [
            TokenConstraint::keyword('stage'),
        ];
    }

    /**
     * @param TokenStream $tokenStream
     *
     * @return bool
     */
    public function assert(TokenStream $tokenStream): bool
    {
        return $tokenStream->assert(...$this->constraints());
    }

    /**
     * @param TokenStream $tokenStream
     *
     * @return Node\StageNode
     */
    public function create(TokenStream $tokenStream): Node\NodeInterface
    {
        $tokenStream->expect(TokenConstraint::keyword('stage'));

        $stage = new Node\StageNode();

        $tokenStream->expect(TokenConstraint::openingBracket());
        if ($tokenStream->assert(...TokenConstraint::anyString())) {
            $stage->label = $tokenStream->consume()->value;
        } else {
            throw Exception\UnexpectedTokenException::expectedString($tokenStream->watch());
        }
        $tokenStream->expect(TokenConstraint::closingBracket());

        $tokenStream->expect(TokenConstraint::openingCurlyBraces());

        while (!$tokenStream->assert(TokenConstraint::closingCurlyBraces())) {
            if ($this->stepCollectionResolution->assert($tokenStream)) {
                $stage->steps = $this->stepCollectionResolution->create($tokenStream);
                continue;
            }

            if ($this->environmentResolution->assert($tokenStream)) {
                $stage->environment = $this->environmentResolution->create($tokenStream);
                continue;
            }

            if ($this->agentResolution->assert($tokenStream)) {
                $stage->agent = $this->agentResolution->create($tokenStream);
                continue;
            }

            if ($this->postActionResolution->assert($tokenStream)) {
                $stage->post = $this->postActionResolution->create($tokenStream);
                continue;
            }

            if ($this->optionsResolution->assert($tokenStream)) {
                $stage->options = $this->optionsResolution->create($tokenStream);
                continue;
            }

            throw Exception\UnexpectedTokenException::unmatchedConstraints(
                $tokenStream->watch(),
                ...$this->stepCollectionResolution->constraints(),
                ...$this->environmentResolution->constraints(),
                ...$this->agentResolution->constraints(),
                ...$this->postActionResolution->constraints(),
                ...$this->optionsResolution->constraints()
            );
        }

        $tokenStream->expect(TokenConstraint::closingCurlyBraces());

        return $stage;
    }
}

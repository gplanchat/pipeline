<?php

namespace Kiboko\Component\Phroovy\AST\TreeResolution;

use Kiboko\Component\Phroovy\AST\Node;
use Kiboko\Component\Phroovy\AST\TokenConstraint;
use Kiboko\Component\Phroovy\AST\TokenStream;

class StepResolution implements TreeResolutionInterface
{
    public function assert(TokenStream $tokenStream): bool
    {
        return $tokenStream->assertAny(TokenConstraint::anyString());
    }

    public function create(TokenStream $tokenStream): Node\NodeInterface
    {
        $step = new Node\StepNode($tokenStream->expect(TokenConstraint::identifier())->value);

        while (true) {
            if (!$tokenStream->assertAny(TokenConstraint::anyString())) {
                break;
            }

            $step->arguments[] = $tokenStream->consume()->value;
        }

        return $step;
    }
}

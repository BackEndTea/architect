<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Infrastructure\PHPParser;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

final class NameUsingCollector extends NodeVisitorAbstract
{
    /** @var array<string> */
    public array $usedNames = [];

    public function enterNode(Node $node): Node|null
    {
        if (! ($node instanceof Node\Name)) {
            return null;
        }

        $this->usedNames[] = $node->name;

        return null;
    }
}

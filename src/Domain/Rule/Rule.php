<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Rule;

use BackEndTea\Architect\Domain\Declaration;
use BackEndTea\Architect\Domain\Matcher;

class Rule implements \BackEndTea\Architect\Domain\Rule
{
    public function __construct(
        private Matcher $from,
        private Matcher $to,
    ) {
    }

    public function isAllowed(Declaration $from, Declaration $to): bool
    {
        // From doesn't match, so we allow it
        if (! $this->from->matches($from)) {
            return true;
        }

        return $this->to->matches($to) === false;
    }
}

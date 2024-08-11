<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Rule;

use BackEndTea\Architect\Domain\Declaration;
use BackEndTea\Architect\Domain\Matcher;
use BackEndTea\Architect\Domain\Matcher\All;
use BackEndTea\Architect\Domain\Matcher\None;
use InvalidArgumentException;

class Rule implements \BackEndTea\Architect\Domain\Rule
{
    public function __construct(
        private Matcher|null $from = null,
        private Matcher|null $notFrom = null,
        private Matcher|null $to = null,
        private Matcher|null $notTo = null,
    ) {
        if ($from === null && $notFrom === null) {
            throw new InvalidArgumentException('Either from or notFrom should be set');
        }

        if ($to === null && $notTo === null) {
            throw new InvalidArgumentException('Either to or notTo should be set');
        }
    }

    public function isAllowed(Declaration $from, Declaration $to): bool
    {
        if (! $this->doesMatch($from, $this->from, $this->notFrom)) {
            return true;
        }

        return ! $this->doesMatch($to, $this->to, $this->notTo);
    }

    private function doesMatch(
        Declaration $declaration,
        Matcher|null $matcher,
        Matcher|null $negatedMatcher,
    ): bool {
        $matcher        ??= new All();
        $negatedMatcher ??=  new None();

        return $matcher->matches($declaration) && $negatedMatcher->matches($declaration) === false;
    }
}

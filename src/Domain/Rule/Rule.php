<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Rule;

use BackEndTea\Architect\Domain\Declaration;
use BackEndTea\Architect\Domain\Matcher;
use BackEndTea\Architect\Domain\Matcher\Always;
use BackEndTea\Architect\Domain\Matcher\None;
use InvalidArgumentException;

class Rule implements \BackEndTea\Architect\Domain\Rule
{
    public function __construct(
        private string $name,
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

    public function isForbidden(Declaration $from, Declaration $to): bool
    {
        return $this->doesMatch($from, $this->from, $this->notFrom)
            && $this->doesMatch($to, $this->to, $this->notTo);
    }

    private function doesMatch(
        Declaration $declaration,
        Matcher|null $matcher,
        Matcher|null $negatedMatcher,
    ): bool {
        $matcher        ??= new Always();
        $negatedMatcher ??=  new None();

        return $matcher->matches($declaration) && $negatedMatcher->matches($declaration) === false;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

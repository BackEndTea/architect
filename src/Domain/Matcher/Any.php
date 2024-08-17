<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Matcher;

use BackEndTea\Architect\Domain\Declaration;
use BackEndTea\Architect\Domain\Matcher;

class Any implements Matcher
{
    /** @var array<Matcher> */
    private readonly array $matchers;

    public function __construct(
        Matcher ...$matchers,
    ) {
        $this->matchers = $matchers;
    }

    public function matches(Declaration $file): bool|null
    {
        foreach ($this->matchers as $matcher) {
            if ($matcher->matches($file)) {
                return true;
            }
        }

        return false;
    }
}

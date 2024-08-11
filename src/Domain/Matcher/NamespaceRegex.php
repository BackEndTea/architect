<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Matcher;

use BackEndTea\Architect\Domain\Declaration;
use BackEndTea\Architect\Domain\Matcher;

use function preg_match;

class NamespaceRegex implements Matcher
{
    public function __construct(
        private readonly string $regex,
    ) {
    }

    public function matches(Declaration $file): bool|null
    {
        if ($file->namespace === null) {
            return null;
        }

        return (bool) preg_match($this->regex, $file->namespace);
    }
}

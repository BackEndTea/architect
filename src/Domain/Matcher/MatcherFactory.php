<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Matcher;

use BackEndTea\Architect\Domain\Matcher;

class MatcherFactory
{
    public static function psrMatcher(): Matcher
    {
        return new NamespaceRegex(
            '/^Psr\\\\.*/',
        );
    }
}

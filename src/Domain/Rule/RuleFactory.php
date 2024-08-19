<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Rule;

use BackEndTea\Architect\Domain\Matcher;
use BackEndTea\Architect\Domain\Matcher\Any;
use BackEndTea\Architect\Domain\Matcher\GlobFile;
use BackEndTea\Architect\Domain\Matcher\NamespaceRegex;
use BackEndTea\Architect\Domain\Matcher\NativePHP;

use function sprintf;

class RuleFactory
{
    public static function noSrcToTest(): \BackEndTea\Architect\Domain\Rule
    {
        return new Rule(
            'no src to test',
            from: new Any(
                new GlobFile('src/**/*'),
                new GlobFile('lib/**/*'),
            ),
            notFrom: new NamespaceRegex('#\\\\Test\\\\#'),
            to: new Any(
                new GlobFile('tests/**/*'),
                new GlobFile('test/**/*'),
                new GlobFile('spec/**/*'),
            ),
        );
    }

    /** @return array<\BackEndTea\Architect\Domain\Rule> */
    public static function layeredArchitecture(): array
    {
        return [
            new Rule(
                'layered architecture - domain',
                from: new NamespaceRegex('#\\\\Domain\\\\#'),
                notFrom: new NamespaceRegex('#\\\\Test\\\\#'),
                to: new Any(
                    new NamespaceRegex('#\\\\Infrastructure\\\\#'),
                    new NamespaceRegex('#\\\\Application\\\\#'),
                ),
            ),
            new Rule(
                'layered architecture - application',
                from: new NamespaceRegex('#\\\\Application\\\\#'),
                notFrom: new NamespaceRegex('#\\\\Test\\\\#'),
                to: new Any(
                    new NamespaceRegex('#\\\\Infrastructure\\\\#'),
                ),
            ),
        ];
    }

    public static function onlySelfAndNative(
        string $namespacePart,
        Matcher ...$additionalAllowedMatchers,
    ): \BackEndTea\Architect\Domain\Rule {
        return new Rule(
            'only self and native',
            from: new NamespaceRegex(sprintf('#\\\\%s\\\\#', $namespacePart)),
            notTo: new Any(
                new NamespaceRegex(sprintf('#\\\\%s\\\\#', $namespacePart)),
                new NativePHP(),
                ...$additionalAllowedMatchers,
            ),
        );
    }

    /**
     * This namespace can have dependencies on other namespaces, but can not be depended on.
     * Usefull if you have a `Leagcy` namespace, where newer code can't use the Legacy classes, but Legacy classes
     * can use the newer code.
     */
    public static function onlyOutward(string $namespacePart): \BackEndTea\Architect\Domain\Rule
    {
        return new Rule(
            'only outward',
            notFrom: new NamespaceRegex(sprintf('#\\\\%s\\\\#', $namespacePart)),
            to: new NamespaceRegex(sprintf('#\\\\%s\\\\#', $namespacePart)),
        );
    }
}

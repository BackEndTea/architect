<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Config;

use BackEndTea\Architect\Domain\Rule;
use SplFileInfo;

class Configuration
{
    /**
     * @param iterable<SplFileInfo> $paths
     * @param non-empty-array<Rule> $rules
     */
    public function __construct(
        public readonly iterable $paths,
        public readonly array $rules,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Rule;

class Error
{
    public function __construct(
        public readonly string $ruleType,
        public readonly string $fileName,
        public readonly int $line,
        public readonly string $fromName,
        public readonly string $toName,
    ) {
    }
}

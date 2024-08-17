<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Config;

use BackEndTea\Architect\Domain\Printer\Printer;
use BackEndTea\Architect\Domain\Rule;
use SplFileInfo;

class Configuration
{
    public string $configLocation = '';

    /**
     * @param iterable<SplFileInfo>    $paths
     * @param non-empty-array<Rule>    $rules
     * @param non-empty-array<Printer> $printers
     */
    public function __construct(
        public readonly iterable $paths,
        public readonly array $rules,
        public readonly array $printers,
    ) {
    }

    public function withConfigLocation(string $configLocation): self
    {
        $this->configLocation = $configLocation;

        return $this;
    }
}

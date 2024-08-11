<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Config;

use BackEndTea\Architect\Domain\Rule;
use InvalidArgumentException;
use SplFileInfo;

use function array_merge;

class ConfigurationBuilder
{
    /** @var iterable<SplFileInfo> */
    private iterable $paths = [];
    /** @var array<Rule>  */
    private array $rules = [];

    public static function create(): self
    {
        return new self();
    }

    /** @param iterable<SplFileInfo> $paths */
    public function paths(iterable $paths): static
    {
        $this->paths = $paths;

        return $this;
    }

    public function addRule(Rule ...$rule): static
    {
        $this->rules = array_merge($this->rules, $rule);

        return $this;
    }

    public function build(): Configuration
    {
        if ($this->paths === []) {
            throw new InvalidArgumentException('Paths cannot be empty');
        }

        if ($this->rules === []) {
            throw new InvalidArgumentException('Rules cannot be empty');
        }

        return new Configuration($this->paths, $this->rules);
    }
}

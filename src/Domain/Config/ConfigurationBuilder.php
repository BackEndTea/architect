<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Config;

use BackEndTea\Architect\Domain\Printer\ConsolePrinter;
use BackEndTea\Architect\Domain\Printer\Printer;
use BackEndTea\Architect\Domain\Rule;
use InvalidArgumentException;
use SplFileInfo;

use function array_merge;
use function is_array;

class ConfigurationBuilder
{
    /** @var iterable<SplFileInfo> */
    private iterable $paths = [];
    /** @var array<Rule>  */
    private array $rules = [];
    /** @var array<Printer>  */
    private array $printers = [];

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

    /**
     * @param Rule|array<Rule> ...$rule
     *
     * @return $this
     */
    public function addRule(Rule|array ...$rule): static
    {
        foreach ($rule as $r) {
            if (is_array($r)) {
                $this->rules = array_merge($this->rules, $r);

                continue;
            }

            $this->rules[] = $r;
        }

        return $this;
    }

    public function addPrinter(Printer ...$printer): static
    {
        $this->printers = array_merge($this->printers, $printer);

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

        if ($this->printers === []) {
            $this->printers[] = new ConsolePrinter();
        }

        return new Configuration($this->paths, $this->rules, $this->printers);
    }
}

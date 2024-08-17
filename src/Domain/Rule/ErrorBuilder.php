<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Rule;

class ErrorBuilder
{
    private string $ruleType = '';

    private string $fileName = '';

    private int $line = 0;

    private string $fromName = '';

    private string $toName = '';

    public static function create(): self
    {
        return new self();
    }

    public function ruleType(string $ruleType): self
    {
        $this->ruleType = $ruleType;

        return $this;
    }

    public function fileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function line(int $line): self
    {
        $this->line = $line;

        return $this;
    }

    public function fromName(string $fromName): self
    {
        $this->fromName = $fromName;

        return $this;
    }

    public function toName(string $toName): self
    {
        $this->toName = $toName;

        return $this;
    }

    public function build(): Error
    {
        return new Error(
            ruleType: $this->ruleType,
            fileName: $this->fileName,
            line: $this->line,
            fromName: $this->fromName,
            toName: $this->toName,
        );
    }
}

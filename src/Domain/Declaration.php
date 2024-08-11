<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain;

class Declaration
{
    public function __construct(
        public readonly string|null $namespace,
    ) {
    }
}

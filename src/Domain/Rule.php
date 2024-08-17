<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain;

interface Rule
{
    public function isForbidden(Declaration $from, Declaration $to): bool;

    public function getName(): string;
}

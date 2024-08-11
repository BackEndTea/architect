<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain;

interface Rule
{
    public function isAllowed(Declaration $from, Declaration $to): bool;
}

<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain;

interface Matcher
{
    public function matches(Declaration $file): bool|null;
}

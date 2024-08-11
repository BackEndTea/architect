<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Matcher;

use BackEndTea\Architect\Domain\Declaration;
use BackEndTea\Architect\Domain\Matcher;

class None implements Matcher
{
    public function matches(Declaration $file): bool|null
    {
        return false;
    }
}

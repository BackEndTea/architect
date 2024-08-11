<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Matcher;

use BackEndTea\Architect\Domain\Declaration;
use BackEndTea\Architect\Domain\Matcher;

use function fnmatch;

class GlobFile implements Matcher
{
    public function __construct(
        private string $path,
    ) {
    }

    public function matches(Declaration $file): bool|null
    {
        $fileName = $file->fileName();
        if (! $fileName) {
            return null;
        }

        return fnmatch($this->path, $fileName);
    }
}

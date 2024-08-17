<?php

declare(strict_types=1);

namespace BackEndTea\Architect\Domain\Matcher;

use BackEndTea\Architect\Domain\Declaration;
use BackEndTea\Architect\Domain\Matcher;

use function fnmatch;
use function str_starts_with;
use function strlen;
use function substr;

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

        if (str_starts_with($fileName, $file->rootDirectory())) {
            // Need to drop the trailing slash. so +1;
            $fileName = substr($fileName, strlen($file->rootDirectory()) + 1);
        }

        return fnmatch($this->path, $fileName);
    }
}

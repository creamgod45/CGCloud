<?php

namespace App\Lib\Utils\CGFileSystem;

use File;
use Symfony\Component\Finder\SplFileInfo;

class CGBaseFolder extends CGBaseFileObject
{
    public function __construct(string $path, bool $defaultFileExists = false)
    {
        parent::__construct($path, $defaultFileExists);
    }

    /**
     * @return string[]
     */
    public function allFiles(): array
    {
        return collect(File::allFiles(self::getPath()))
            ->map(function (SplFileInfo $file) {
                return $file->getPathname();
            })
            ->toArray();
    }
}
